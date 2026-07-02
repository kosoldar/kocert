@php
// ── Helpers ──────────────────────────────────────────────────────────────────
$vars = $cert->variables ?? [];
$v    = fn($k, $d = '') => $vars[$k] ?? $d;

$norma    = $cert->norma->nombre;
$nagCat   = strtoupper($v('nag_categoria', ''));
$nagCred  = $v('nag_credencial', '');

$esC = str_contains($norma, 'Cat. C') || str_contains($norma, 'Cat C');
$esD = str_contains($norma, 'Cat. D') || str_contains($norma, 'Cat D');

// Test results
$tr       = fn($code) => $cert->tests->first(fn($t) => optional($t->testType)->code === $code)?->resultado ?? '';
$aprobado = fn($code) => strtolower($tr($code)) === 'aprobado';
$naResult = fn($code) => strtolower($tr($code)) === 'na';

// Ranges
$rango    = fn($type) => $cert->ranges->firstWhere('type', $type)?->descripcion ?? '';
$posRangs = $cert->ranges->where('type', 'posicion')->pluck('descripcion')->join(' | ');

// Dates
$meses = [1=>'ENERO',2=>'FEBRERO',3=>'MARZO',4=>'ABRIL',5=>'MAYO',6=>'JUNIO',
          7=>'JULIO',8=>'AGOSTO',9=>'SEPTIEMBRE',10=>'OCTUBRE',11=>'NOVIEMBRE',12=>'DICIEMBRE'];
$fechaCalif = $cert->fecha_calificacion
    ? sprintf('%02d/%s/%d',
        $cert->fecha_calificacion->day,
        $meses[$cert->fecha_calificacion->month],
        $cert->fecha_calificacion->year)
    : '';
$fechaVto = $cert->fecha_vencimiento?->format('d-m-y') ?? '';

// NAG edition text
$normaEdicion = 'NAG 105 (ENARGAS) — Resolución I/0066/2023';

// Welding variables
$proceso     = strtoupper($cert->proceso ?? '');
$tipoUso     = strtoupper($v('tipo', 'MANUAL'));
$respaldo    = strtoupper($v('respaldo', ''));
$respaldoRng = stripos($respaldo, 'SIN') !== false ? 'CON y SIN RESPALDO' : $respaldo;
$pNumber     = strtoupper($v('p_number', $v('grupo_base_metal', '')));
$pNumRng     = $rango('grupo_base_metal') ?: $pNumber;
$electrodo   = strtoupper($v('electrodo', $v('electrodo_raiz', '')));
$fNumber     = strtoupper($v('f_number', $v('grupo_consumible', '')));
$fNumRng     = $rango('grupo_consumible') ?: $fNumber;
$aNumber     = strtoupper($v('a_number', ''));
$corriente   = strtoupper($v('corriente', $v('corriente_raiz', '')));
$gas         = strtoupper($v('gas_proteccion', 'NO')) ?: 'NO';
$espDep      = $v('espesor_deposito', '');
$espMaxCal   = $espDep ? round((float)$espDep * 2, 1) . ' mm' : '';
$espCupon    = $v('espesor_cupon', '');
$metalBase   = strtoupper($v('metal_base', ''));
$posicion    = strtoupper($cert->posicion ?? '');
$progresion  = $cert->progresion ? strtoupper($cert->progresion) : '';
$posTest     = trim("{$posicion} {$progresion}");

// Joint design
$svgRaw       = $cert->jointDesign?->svg ?? '';
$svg          = preg_replace('/(<svg[^>]*?)(\s+width="[^"]*"|\s+height="[^"]*")/i', '$1', $svgRaw);
$juntaNombre  = strtoupper($cert->jointDesign?->nombre ?? '');
$juntaDetalle = $cert->joint_detail ?? '';
$juntaRng     = strtoupper($v('junta_calificada', ''));

// Coupon
$esCanio = $cert->tipo_cupon === 'caño';
$esChapa = $cert->tipo_cupon === 'chapa';
$couponStr     = $espCupon ? $espCupon . ' mm' : '';
$diamStr       = $v('diametro_cupon', '') ? 'Ø' . $v('diametro_cupon') . ' mm' : '';
$couponDisplay = $esCanio ? "Diámetro/Espesor: {$diamStr}" . ($couponStr ? " – Esp {$couponStr}" : '')
                           : "Espesor: {$couponStr}";
$rangoEsp = $rango('espesor');
$rangoCalDisplay = $esCanio
    ? "CALIFICA: Ø ≥ " . ($cert->ranges->firstWhere('type', 'diametro')?->min_value ?? '') . " mm  Esp: {$rangoEsp}"
    : "CALIFICA: Espesores: {$rangoEsp}";

// Inspector / welder
$inspNombre = $cert->inspector?->nombre ?? '';
$inspCert   = $cert->inspector?->certificacion ?? '';
$inspTel    = $cert->inspector?->telefono ?? '';
$inspEmail  = $cert->inspector?->email ?? '';
$apellido        = strtoupper($cert->soldador?->apellido ?? '');
$nombre          = strtoupper($cert->soldador?->nombre ?? '');
$dni             = $cert->soldador?->dni ?? '';
$fechaNac        = $cert->soldador?->fecha_nacimiento
    ? \Carbon\Carbon::parse($cert->soldador->fecha_nacimiento)->format('d/m/Y')
    : '';
$nacionalidad    = strtoupper($cert->soldador?->nacionalidad ?? '');
$cuno            = $cert->soldador?->cuño ?? '';
$empresa    = $cert->empresa?->nombre ?? '';
$rcsNumero  = $cert->numero . '-' . substr((string)$cert->anio, -2) . '-Rev. ' . $cert->revision;

$chk = fn(bool $c) => $c ? '&#9632;' : '&#9633;';

// NAG Cat. D — verificación tensión SMYS (EPS N°1): i = P×D×100 / (2×t×S)
$presionD    = (float)($vars['presion_diseno']   ?? 0);
$smys        = (float)($vars['tension_fluencia'] ?? 0);
$dCupon      = (float)($vars['diametro_cupon']   ?? 0);
$tCupon      = (float)($vars['espesor_cupon']    ?? 0);
$smysResult  = ($presionD > 0 && $smys > 0 && $dCupon > 0 && $tCupon > 0)
    ? round(($presionD * $dCupon * 100) / (2 * $tCupon * $smys), 2)
    : null;
$smysOk      = $smysResult !== null && $smysResult < 20;

// Alcance de diámetro — Form 513-780-0 reverso (NAG §6.4)
// NAG usa 2"=50,8mm y 12"=304,8mm — distinto de API 1104 (60,3/323,9mm)
$dMinVal    = $cert->ranges->firstWhere('type', 'diametro')?->min_value;
$alcanceDiam = match(true) {
    $dMinVal === null           => '—',
    (float)$dMinVal < 50.8     => '< 2"  (< 50,8 mm)',
    (float)$dMinVal <= 304.8   => '2" – 12"  (50,8–304,8 mm)',
    default                    => '> 12"  (> 304,8 mm)',
};
@endphp
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<style>
* { box-sizing: border-box; }
body { font-family: Arial, sans-serif; font-size: 8pt; color: #000; margin: 0; padding: 0; }
table { border-collapse: collapse; width: 100%; }
td, th { vertical-align: top; }
.b { border: 0.3mm solid #000; }
.bold { font-weight: bold; }
.center { text-align: center; }
.right { text-align: right; }
.middle { vertical-align: middle; }
.p2 { padding: 1mm 2mm; }
.p1 { padding: 1mm; }
.label { font-size: 6.5pt; }
.label-bold { font-size: 6.5pt; font-weight: bold; }
.value-bold { font-size: 8.5pt; font-weight: bold; }
.welder-name { font-size: 15pt; font-weight: bold; }
.rcs-number { font-size: 9pt; font-weight: bold; }
.vto-label { font-size: 7pt; font-weight: bold; color: #CC0000; }
.vto-value { font-size: 14pt; font-weight: bold; color: #CC0000; }
.nag-label { font-size: 7pt; font-weight: bold; color: #005A9C; }
.nag-value { font-size: 10pt; font-weight: bold; color: #005A9C; }
.nag-warning { font-size: 6.5pt; color: #CC0000; font-weight: bold; }
.aprobado-stamp { font-size: 12pt; font-weight: bold; }
.cert-text { font-size: 6.5pt; text-align: justify; }
.verify-text { font-size: 7pt; font-weight: bold; color: #CC0000; text-align: center; }
.section-title { font-size: 9pt; font-weight: bold; text-decoration: underline; text-align: center; }
.result-line { font-size: 7.5pt; margin: 1.5mm 0; }
.law-text { font-size: 6.5pt; color: #CC0000; text-align: center; }
</style>
</head>
<body>

{{-- ═══════════════════════════════════════════════════════ PÁGINA 1 ══ --}}

{{-- HEADER --}}
<table style="border:0.3mm solid #000;">
  <tr>
    <td width="28%" style="border-right:0.3mm solid #000;" class="p2">
      @if($kosoldarLogoDataUri)
        <img src="{{ $kosoldarLogoDataUri }}" style="height:12mm;max-width:35mm;" /><br>
      @endif
      <span class="bold" style="font-size:7.5pt;">{{ $inspNombre }}</span><br>
      <span class="label">Móvil {{ $inspTel }}</span><br>
      <span class="label">{{ $inspEmail }}</span>
    </td>

    <td width="44%" style="border-right:0.3mm solid #000;text-align:center;" class="p2 middle">
      <div style="font-size:8pt;font-style:italic;">{{ $normaEdicion }}</div>
      <div style="font-size:11pt;font-weight:bold;margin:1mm 0;">REGISTRO DE CALIFICACION DE SOLDADORES</div>
      <div style="font-size:10pt;font-weight:bold;">(RCS) — {{ $norma }}</div>
      <div style="font-size:8pt;">CERTIFICACIÓN DE SOLDADOR HABILITADO ENARGAS</div>
    </td>

    <td width="28%" style="text-align:center;" class="p2">
      @if($qrDataUri)
        <img src="{{ $qrDataUri }}" style="width:22mm;height:22mm;" /><br>
      @endif
      <div style="font-size:7pt;margin-top:1mm;">RCS / NAG 105</div>
      <div class="rcs-number">Nº {{ $rcsNumero }}</div>
    </td>
  </tr>
</table>

{{-- NAG CREDENCIAL ROW --}}
<table style="margin-top:1mm;border:0.3mm solid #000;">
  <tr>
    <td width="30%" class="b p1">
      <span class="nag-label">Categoría NAG 105</span><br>
      <span class="nag-value">{{ $nagCat ?: '—' }}</span>
    </td>
    <td width="40%" class="b p1">
      <span class="nag-label">N° Credencial NAG (Form 513-780-0)</span><br>
      <span class="nag-value">{{ $nagCred ?: '—' }}</span>
    </td>
    <td width="30%" class="b p1">
      <span class="nag-warning">
        Vigencia máx: 2 años (NAG 105 Art. 5.3)<br>
        Inactividad &gt;90 días requiere re-habilitación
      </span>
    </td>
  </tr>
</table>

{{-- PROCESS ROW --}}
<table style="margin-top:1mm;border:0.3mm solid #000;">
  <tr>
    <td width="45%" class="p1 b">
      <span class="label">Welding process(es): Type</span><br>
      <span class="label-bold">Proceso(s) de Soldadura: Tipo</span>
    </td>
    <td width="30%" class="p1 b center middle">
      <span style="font-size:14pt;font-weight:bold;">{{ $proceso }}</span>
    </td>
    <td width="25%" class="p1 b center middle">
      <span style="font-size:11pt;font-weight:bold;">{{ $tipoUso }}</span>
    </td>
  </tr>
</table>

{{-- WELDER --}}
<table style="margin-top:1mm;border:0.3mm solid #000;">
  <tr>
    <td width="72%" style="border-right:0.3mm solid #000;" class="p2">
      <table style="border:none;">
        <tr>
          <td style="border:none;" class="p1">
            <span class="label">Nombre del Soldador / Stamp / CUÑO</span>
          </td>
          <td style="border:none;" class="p1">
            <div class="welder-name">{{ $apellido }}</div>
            <div class="welder-name">{{ $nombre }}</div>
            @if($cuno)<div style="font-size:8pt;">{{ $cuno }}</div>@endif
          </td>
        </tr>
      </table>
      <table style="border:none;margin-top:1mm;">
        <tr>
          <td width="50%" style="border:none;" class="p1">
            <span class="label">EMPRESA:</span><br>
            <span class="value-bold">{{ $empresa ?: 'PARTICULAR' }}</span>
          </td>
          <td width="50%" style="border:none;" class="p1">
            <span class="label">Nº Documento:</span>
            <span class="value-bold">{{ $dni }}</span><br>
            @if($fechaNac)<span class="label">F. Nac.: </span><span class="bold">{{ $fechaNac }}</span>&nbsp;&nbsp;@endif
            @if($nacionalidad)<span class="label">Nac.: </span><span class="bold">{{ $nacionalidad }}</span>@endif
          </td>
        </tr>
        <tr>
          <td width="50%" style="border:none;" class="p1">
            <span class="label"><strong>EPS Aplicado:</strong></span><br>
            <span class="value-bold">{{ $cert->eps_numero }}</span>
          </td>
          @if($cert->pqr_numero)
          <td width="50%" style="border:none;" class="p1">
            <span class="label"><strong>RCP:</strong></span><br>
            <span class="value-bold">{{ $cert->pqr_numero }}</span>
          </td>
          @endif
        </tr>
        <tr>
          <td width="50%" style="border:none;" class="p1">
            <span class="label"><strong>Fecha de Calificación</strong></span><br>
            <span class="value-bold">{{ $fechaCalif }}</span>
          </td>
          <td width="50%" style="border:none;" class="p1">
            <span class="vto-label">FECHA DE VENCIMIENTO</span><br>
            <span class="vto-value">{{ $fechaVto }}</span>
          </td>
        </tr>
        <tr>
          <td colspan="2" style="border:none;" class="p1">
            <span class="label">Ampliación/Renovación {!! $chk($cert->tipo !== 'inicial') !!}</span>
          </td>
        </tr>
      </table>
    </td>
    <td width="28%" class="p2 center middle">
      @if($fotoDataUri)
        <img src="{{ $fotoDataUri }}" style="max-width:42mm;max-height:52mm;" />
      @else
        <div style="border:0.3mm solid #ccc;width:42mm;height:52mm;text-align:center;font-size:7pt;color:#888;"><br><br><br>Foto DNI</div>
      @endif
    </td>
  </tr>
</table>

{{-- COUPON --}}
<table style="margin-top:1mm;border:0.3mm solid #000;">
  <tr>
    <td width="22%" class="b p1">
      <span class="label"><strong>Cupón de prueba</strong></span><br>
      <div style="margin-top:1mm;font-size:7pt;">{!! $chk($esCanio) !!} Caño</div>
      <div style="font-size:7pt;">{!! $chk($esChapa) !!} Chapa</div>
    </td>
    <td width="22%" class="b p1">
      <span class="label">Soldadura de producción</span>
    </td>
    <td width="56%" class="b p1">
      <span class="label"><strong>Especificación del Metal Base:</strong></span>
      <span class="value-bold"> {{ $metalBase }}</span><br>
      <div style="margin-top:1mm;font-size:7.5pt;">
        {{ $couponDisplay }}&nbsp;&nbsp;
        <strong>{{ $rangoCalDisplay }}</strong>
      </div>
    </td>
  </tr>
</table>

{{-- CERT TEXT --}}
<div style="margin-top:2mm;" class="cert-text">
  Certificamos que las declaraciones en este registro son correctas y que los cupones de prueba fueron
  preparados, soldados y ensayados de acuerdo a los requisitos de la norma <strong>{{ $normaEdicion }}</strong>
  y la normativa ENARGAS vigente.
</div>
<div class="verify-text" style="margin-top:1mm;">
  VERIFICAR LA AUTENTICIDAD DE ESTE DOCUMENTO ESCANEANDO EL CODIGO QR
</div>
<div class="label right" style="margin-top:0.5mm;">Página 1 de 2</div>

{{-- VARIABLES TABLE --}}
<table style="margin-top:2mm;border:0.3mm solid #000;">
  <thead>
    <tr style="background:#e8e8e8;">
      <th width="34%" class="b p1 center"><span class="label-bold">Variables de Soldadura</span></th>
      <th width="30%" class="b p1 center"><span class="label-bold">Condiciones de Ensayo</span></th>
      <th width="28%" class="b p1 center"><span class="label-bold">Rangos Calificados</span></th>
      <th width="8%"  class="b p1 center"><span class="label-bold">Obs.</span></th>
    </tr>
  </thead>
  <tbody>
    <tr>
      <td class="b p1"><span class="label"><strong>Tipo Usado (manual, semi-auto)</strong></span></td>
      <td class="b p1 center middle"><span class="bold">{{ $tipoUso }}</span></td>
      <td class="b p1 center middle"><span class="bold">{{ $tipoUso }}</span></td>
      <td class="b p1"></td>
    </tr>

    @if(!$esD)
    <tr>
      <td class="b p1"><span class="label"><strong>Respaldo</strong></span></td>
      <td class="b p1 center middle"><span class="bold">{{ $respaldo }}</span></td>
      <td class="b p1 center middle"><span class="bold">{{ $respaldoRng }}</span></td>
      <td class="b p1"></td>
    </tr>

    <tr>
      <td class="b p1"><span class="label"><strong>Metal Base — Número P ó S / Grupo</strong></span></td>
      <td class="b p1 center middle"><span class="bold">{{ $pNumber }}</span></td>
      <td class="b p1 middle"><span style="font-size:6.5pt;">{{ $pNumRng }}</span></td>
      <td class="b p1"></td>
    </tr>

    <tr>
      <td class="b p1"><span class="label"><strong>Electrodo / Metal de Aporte</strong></span></td>
      <td class="b p1 center middle" colspan="2"><span class="bold">{{ $electrodo }}</span></td>
      <td class="b p1"></td>
    </tr>

    <tr>
      <td class="b p1"><span class="label"><strong>Grupo consumible / F-Number</strong></span></td>
      <td class="b p1 center middle"><span class="bold">{{ $fNumber }}</span></td>
      <td class="b p1 middle"><span style="font-size:6.5pt;">{{ $fNumRng }}</span></td>
      <td class="b p1 center middle"><span class="bold">{{ $aNumber }}</span></td>
    </tr>

    <tr>
      <td class="b p1">
        <span class="label-bold">DISEÑO DE JUNTA</span>
      </td>
      <td class="b p1">
        @if($svg)
          <div style="width:55mm;height:30mm;overflow:hidden;">{!! $svg !!}</div>
        @endif
        <div class="bold" style="font-size:7.5pt;margin-top:1mm;">{{ $juntaNombre }}</div>
        @if($juntaDetalle)<div style="font-size:7pt;">{{ $juntaDetalle }}</div>@endif
      </td>
      <td class="b p1 middle">
        @if($juntaRng)<span class="bold" style="font-size:7.5pt;">{{ $juntaRng }}</span>@endif
      </td>
      <td class="b p1"></td>
    </tr>

    @if($espDep)
    <tr>
      <td class="b p1"><span class="label"><strong>Espesor depositado para cada proceso</strong></span></td>
      <td class="b p1 center middle"><span class="bold">{{ $espDep }} mm</span></td>
      <td class="b p1 middle">
        <span class="label"><strong>Espesor máx calificado:</strong> {{ $espMaxCal }}</span>
      </td>
      <td class="b p1"></td>
    </tr>
    @endif
    @endif

    <tr>
      <td class="b p1"><span class="label"><strong>Posición / Progresión</strong></span></td>
      <td class="b p1 center middle"><span class="bold">{{ $posTest }}</span></td>
      <td class="b p1 center middle"><span class="bold" style="font-size:7pt;">{{ $posRangs }}</span></td>
      <td class="b p1"></td>
    </tr>

    @if(!$esD)
    <tr>
      <td class="b p1"><span class="label"><strong>Corriente / Polaridad</strong></span></td>
      <td class="b p1 center middle"><span class="bold">{{ $corriente }}</span></td>
      <td class="b p1"></td>
      <td class="b p1"></td>
    </tr>

    <tr>
      <td class="b p1"><span class="label">Gas protección (GTAW/GMAW)</span></td>
      <td class="b p1 center middle"><span class="bold">{{ $gas }}</span></td>
      <td class="b p1 center middle"><span class="bold">{{ $gas }}</span></td>
      <td class="b p1"></td>
    </tr>
    @endif
  </tbody>
</table>

{{-- ══════════════════════════════════════════════════════ PÁGINA 2 ══ --}}
<pagebreak />

{{-- HEADER (repetido) --}}
<table style="border:0.3mm solid #000;">
  <tr>
    <td width="28%" style="border-right:0.3mm solid #000;" class="p2">
      @if($kosoldarLogoDataUri)
        <img src="{{ $kosoldarLogoDataUri }}" style="height:12mm;max-width:35mm;" /><br>
      @endif
      <span class="bold" style="font-size:7.5pt;">{{ $inspNombre }}</span><br>
      <span class="label">Móvil {{ $inspTel }}</span><br>
      <span class="label">{{ $inspEmail }}</span>
    </td>
    <td width="44%" style="border-right:0.3mm solid #000;text-align:center;" class="p2 middle">
      <div style="font-size:8pt;font-style:italic;">{{ $normaEdicion }}</div>
      <div style="font-size:11pt;font-weight:bold;margin:1mm 0;">REGISTRO DE CALIFICACION DE SOLDADORES</div>
      <div style="font-size:10pt;font-weight:bold;">(RCS) — {{ $norma }}</div>
    </td>
    <td width="28%" style="text-align:center;" class="p2">
      @if($qrDataUri)
        <img src="{{ $qrDataUri }}" style="width:22mm;height:22mm;" /><br>
      @endif
      <div style="font-size:7pt;margin-top:1mm;">RCS / NAG 105</div>
      <div class="rcs-number">Nº {{ $rcsNumero }}</div>
    </td>
  </tr>
</table>

{{-- Welder recap --}}
<table style="margin-top:1mm;border:0.3mm solid #000;">
  <tr>
    <td width="50%" class="b p1">
      <span class="label">Soldador:</span> <span class="bold">{{ $apellido }} {{ $nombre }}</span><br>
      <span class="label">DNI:</span> <span class="bold">{{ $dni }}</span>&nbsp;&nbsp;
      <span class="label">EPS:</span> <span class="bold">{{ $cert->eps_numero }}</span>
    </td>
    <td width="25%" class="b p1">
      <span class="label">Calificación:</span><br>
      <span class="bold">{{ $fechaCalif }}</span>
    </td>
    <td width="25%" class="b p1">
      <span class="vto-label">VENCIMIENTO:</span><br>
      <span class="vto-value">{{ $fechaVto }}</span>
    </td>
  </tr>
</table>

{{-- NAG credencial recap --}}
<table style="margin-top:1mm;border:0.3mm solid #000;">
  <tr>
    <td width="22%" class="b p1">
      <span class="nag-label">Categoría NAG</span><br>
      <span class="nag-value">{{ $nagCat ?: '—' }}</span>
    </td>
    <td width="33%" class="b p1">
      <span class="nag-label">N° Credencial NAG</span><br>
      <span class="nag-value">{{ $nagCred ?: '—' }}</span>
    </td>
    <td width="23%" class="b p1">
      <span class="nag-label">Alcance de diámetro</span><br>
      <span class="bold" style="font-size:8pt;">{{ $alcanceDiam }}</span>
    </td>
    <td width="22%" class="b p1 middle">
      <span class="nag-warning">Vigencia máx 2 años — Inactividad &gt;90 días requiere re-habilitación</span>
    </td>
  </tr>
</table>

{{-- RESULTADOS --}}
<div class="section-title" style="margin-top:3mm;">
  <em>RESULTADOS DE ENSAYOS</em>
</div>

<div style="margin-top:2mm;">
  <div class="result-line">
    {!! $chk($aprobado('VT')) !!}
    <span class="label"><strong>Examen visual de la soldadura completa:</strong></span>
    @if($tr('VT')) <span class="bold">{{ strtoupper($tr('VT')) }}</span> @endif
  </div>

  @if($esD)
  {{-- Cat. D (EPS N°1): requiere nick-break + doblez raíz --}}
  <div class="result-line">
    {!! $chk($aprobado('NB')) !!}
    <span class="label"><strong>Nick Break (ensayo de fractura):</strong></span>
    @if($tr('NB')) <span class="bold">{{ strtoupper($tr('NB')) }}</span> @endif
  </div>
  <div class="result-line">
    {!! $chk($aprobado('BT-R')) !!}
    <span class="label"><strong>Doblez de raíz:</strong></span>
    @if($tr('BT-R')) <span class="bold">{{ strtoupper($tr('BT-R')) }}</span> @endif
  </div>
  @if($smysResult !== null)
  <div class="result-line" style="margin-top:2mm;padding:1mm;border:0.3mm solid {{ $smysOk ? '#005A9C' : '#CC0000' }};">
    <span class="label"><strong>Verificación tensión SMYS (EPS N°1):</strong></span>
    <span class="bold" style="color:{{ $smysOk ? '#005A9C' : '#CC0000' }};">
      i = {{ $presionD }} × {{ $dCupon }} × 100 / (2 × {{ $tCupon }} × {{ $smys }}) = <strong>{{ $smysResult }}%</strong>
      — {{ $smysOk ? 'CUMPLE (< 20% SMYS)' : 'NO CUMPLE (≥ 20% SMYS)' }}
    </span>
  </div>
  @endif
  @else
  <div class="result-line">
    {!! $chk($aprobado('BT-R') || $aprobado('BT-F')) !!}
    <span class="label"><strong>Prueba de plegado / Doblado:</strong></span>
    @if($tr('BT-R') || $tr('BT-F'))
      <span class="bold">{{ strtoupper($tr('BT-R') ?: $tr('BT-F')) }}</span>
    @endif
    &nbsp;&nbsp;
    {!! $chk($aprobado('BT-R')) !!}
    <span class="label">Transversal raíz y cara</span>
  </div>

  @if($esC)
  <div class="result-line">
    {!! $chk($aprobado('NB')) !!}
    <span class="label"><strong>Nick Break (ensayo de fractura):</strong></span>
    @if($tr('NB')) <span class="bold">{{ strtoupper($tr('NB')) }}</span> @endif
  </div>
  @endif

  <div class="result-line">
    {!! $chk($aprobado('RT')) !!}
    <span class="label"><strong>Radiografía (alternativa):</strong></span>
    <span class="bold">{{ $naResult('RT') ? 'NA' : strtoupper($tr('RT')) }}</span>
  </div>
  @endif
</div>

{{-- NOTAS NAG --}}
<div style="margin-top:3mm;border:0.3mm solid #005A9C;padding:2mm;">
  <div class="nag-label" style="margin-bottom:1mm;">NOTAS REGLAMENTARIAS NAG 105 / ENARGAS</div>
  <div style="font-size:7pt;">
    1. Vigencia máxima de 2 (dos) años desde la fecha de calificación (Art. 5.3 NAG 105).<br>
    2. El soldador que deje de soldar en el tipo de unión calificada por un período superior a 90 días
       deberá re-habilitarse antes de retomar tareas (Art. 5.4 NAG 105).<br>
    3. Esta certificación es válida exclusivamente para la categoría indicada y el proceso declarado.<br>
    4. El Inspector certifica que los ensayos fueron realizados bajo supervisión y conforme a la normativa ENARGAS vigente.
  </div>
  @if($cert->observaciones)
    <div style="font-size:7pt;margin-top:1mm;">{{ $cert->observaciones }}</div>
  @endif
</div>

{{-- RESULTADO STAMP --}}
@if(strtolower($cert->resultado) === 'aprobado')
<div style="margin-top:4mm;text-align:center;">
  <div class="aprobado-stamp">SOLDADOR HABILITADO — APROBADO</div>
  <div style="font-size:9pt;">QUALIFIED WELDER — NAG 105 / ENARGAS</div>
</div>
@else
<div style="margin-top:4mm;text-align:center;">
  <div style="font-size:12pt;font-weight:bold;color:#CC0000;">SOLDADOR NO HABILITADO — RECHAZADO</div>
</div>
@endif

{{-- FIRMA --}}
<div style="margin-top:5mm;text-align:center;">
  @if($firmaDataUri)
    <img src="{{ $firmaDataUri }}" style="max-height:20mm;max-width:60mm;" /><br>
  @else
    <div style="height:20mm;"></div>
  @endif
  <div style="font-size:9pt;font-weight:bold;font-style:italic;">{{ $inspNombre }}</div>
  <div style="font-size:8pt;">{{ $inspCert }}</div>
  <div class="law-text" style="margin-top:1mm;">
    Firma y Documento CODIFICADOS - Ley 25.506 régimen internacional de reconocimiento
    de documentos con firma digital y firma electrónica
  </div>
</div>

{{-- CERT TEXT pág 2 --}}
<div style="margin-top:3mm;" class="cert-text">
  Certificamos que las declaraciones en este registro son correctas y que los cupones de prueba fueron
  preparados, soldados y ensayados de acuerdo a los requisitos de la norma <strong>{{ $normaEdicion }}</strong>
  y la normativa ENARGAS vigente.
</div>
<div class="verify-text" style="margin-top:1mm;">
  VERIFICAR LA AUTENTICIDAD DE ESTE DOCUMENTO ESCANEANDO EL CODIGO QR
</div>
<div class="label right" style="margin-top:0.5mm;">Página 2 de 2</div>

</body>
</html>
