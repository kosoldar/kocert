@php
// ── Helpers ──────────────────────────────────────────────────────────────────
$vars = $cert->variables ?? [];
$v    = fn($k, $d = '') => $vars[$k] ?? $d;

$norma   = $cert->norma->nombre;
$esAsme  = in_array($norma, ['ASME IX', 'ASME B31.3', 'ASME B31.8']);
$esAws   = in_array($norma, ['AWS D1.1', 'AWS D1.3', 'AWS D1.6']);

// Test results
$tr       = fn($code) => $cert->tests->first(fn($t) => optional($t->testType)->code === $code)?->resultado ?? '';
$aprobado = fn($code) => strtolower($tr($code)) === 'aprobado';
$naResult = fn($code) => strtolower($tr($code)) === 'na';

// Ranges
$rango    = fn($type) => $cert->ranges->firstWhere('type', $type)?->descripcion ?? '';
$posRangs = $cert->ranges->where('type', 'posicion')->pluck('descripcion')->join(' | ');

// Date formatting DD/MESENMAYÚSCULAS/YYYY y DD-MM-YY
$meses = [1=>'ENERO',2=>'FEBRERO',3=>'MARZO',4=>'ABRIL',5=>'MAYO',6=>'JUNIO',
          7=>'JULIO',8=>'AGOSTO',9=>'SEPTIEMBRE',10=>'OCTUBRE',11=>'NOVIEMBRE',12=>'DICIEMBRE'];
$fechaCalif = $cert->fecha_calificacion
    ? sprintf('%02d/%s/%d',
        $cert->fecha_calificacion->day,
        $meses[$cert->fecha_calificacion->month],
        $cert->fecha_calificacion->year)
    : '';
$fechaVto = $cert->fecha_vencimiento?->format('d-m-y') ?? '';

// Norma edition text
$normaEdicion = match($norma) {
    'ASME IX'    => 'Section IX, ASME Boiler and Pressure Vessel Code) Ad 2023',
    'ASME B31.3' => 'ASME B31.3 Ed 2022',
    'ASME B31.8' => 'ASME B31.8 Ed 2022',
    'AWS D1.1'   => 'AWS D1.1 Ad 2020',
    'AWS D1.6'   => 'AWS D1.6 Ad 2020',
    'AWS D1.3'   => 'AWS D1.3 Ad 2018',
    default      => $norma,
};
$certTextEn = match(true) {
    $esAsme => 'Section IX of the ASME Code. Ed 2023',
    default => $normaEdicion,
};
$certTextEs = match(true) {
    $esAsme => 'la Sección IX del Código ASME. Ed 2023',
    default => $normaEdicion,
};

// Variables de soldadura
$proceso     = strtoupper($cert->proceso ?? '');
$tipoUso     = strtoupper($v('tipo', 'MANUAL'));
$respaldo    = strtoupper($v('respaldo', ''));
$respaldoRng = stripos($respaldo, 'SIN') !== false ? 'CON y SIN RESPALDO' : $respaldo;
$pNumber     = strtoupper($v('p_number', $v('grupo_base_metal', '')));
$pNumRng     = $rango('grupo_base_metal') ?: $pNumber;
$electrodo   = strtoupper($v('electrodo', ''));
$fNumber     = strtoupper($v('f_number', ''));
$fNumRng     = $rango('grupo_consumible') ?: $fNumber;
$aNumber     = strtoupper($v('a_number', ''));
$tungsteno   = strtoupper($v('tungsteno', 'NO')) ?: 'NO';
$corriente   = strtoupper($v('corriente', ''));
$gas         = strtoupper($v('gas_proteccion', 'NO')) ?: 'NO';
$gasResp     = strtoupper($v('gas_respaldo', 'NO')) ?: 'NO';
$espDep      = $v('espesor_deposito', '');
$espMaxCal   = $espDep ? round((float)$espDep * 2, 1) . ' mm' : '';
$espCupon    = $v('espesor_cupon', '');
$metalBase   = strtoupper($v('metal_base', $v('especificacion_metal_base', '')));
$posicion    = strtoupper($cert->posicion ?? '');
$progresion  = $cert->progresion ? strtoupper($cert->progresion) : '';
$posTest     = trim("{$posicion} {$progresion}");
$posRngText  = $esAws ? 'VER TABLA 6.10' : $posRangs;

// Joint design SVG (strip fixed width/height so CSS controls size)
$svgRaw = $cert->jointDesign?->svg ?? '';
$svg = preg_replace('/(<svg[^>]*?)(\s+width="[^"]*"|\s+height="[^"]*")/i', '$1', $svgRaw);
$juntaNombre  = strtoupper($cert->jointDesign?->nombre ?? '');
$juntaDetalle = $cert->joint_detail ?? '';
$juntaRng     = strtoupper($v('junta_calificada', ''));

// Coupon section
$esCanio = $cert->tipo_cupon === 'caño';
$esChapa = $cert->tipo_cupon === 'chapa';
$couponStr = $espCupon ? $espCupon . ' mm' : '';
$diamStr   = $v('diametro_cupon', '') ? 'Ø' . $v('diametro_cupon') . ' mm' : '';
$couponDisplay = $esCanio ? "Diámetro/Espesor: {$diamStr}" . ($couponStr ? " – Esp {$couponStr}" : '')
                           : "Espesor: {$couponStr}";
$rangoEsp = $rango('espesor');
$rangoCalDisplay = $esCanio
    ? "CALIFICA: Ø ≥ " . ($cert->ranges->firstWhere('type', 'diametro')?->min_value ?? '') . " mm  Esp: {$rangoEsp}"
    : "CALIFICA: Espesores: {$rangoEsp}";

// Inspector
$inspNombre = $cert->inspector?->nombre ?? '';
$inspCert   = $cert->inspector?->certificacion ?? '';
$inspTel    = $cert->inspector?->telefono ?? '';
$inspEmail  = $cert->inspector?->email ?? '';

// Welder
$apellido = strtoupper($cert->soldador?->apellido ?? '');
$nombre   = strtoupper($cert->soldador?->nombre ?? '');
$dni      = $cert->soldador?->dni ?? '';
$cuno     = $cert->soldador?->cuño ?? '';
$empresa  = $cert->empresa?->nombre ?? '';
$esParticular = !$empresa || strtolower($empresa) === 'particular';

// RCS Code
$rcsNumero = $cert->numero . '-' . substr((string)$cert->anio, -2) . '-Rev. ' . $cert->revision;

// Checkbox helper
$chk = fn(bool $c) => $c ? '&#9632;' : '&#9633;';
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
    {{-- Left: Inspector info --}}
    <td width="28%" style="border-right:0.3mm solid #000;" class="p2">
      @if($kosoldarLogoDataUri)
        <img src="{{ $kosoldarLogoDataUri }}" style="height:12mm;max-width:35mm;" /><br>
      @endif
      <span class="bold" style="font-size:7.5pt;">{{ $inspNombre }}</span><br>
      <span class="label">Móvil {{ $inspTel }}</span><br>
      <span class="label">{{ $inspEmail }}</span>
    </td>

    {{-- Center: Norma logo + title --}}
    <td width="44%" style="border-right:0.3mm solid #000;text-align:center;" class="p2 middle">
      @if($normaLogoDataUri)
        <img src="{{ $normaLogoDataUri }}" style="height:10mm;max-width:30mm;" /><br>
      @endif
      <div style="font-size:8pt;font-style:italic;">{{ $normaEdicion }}</div>
      <div style="font-size:11pt;font-weight:bold;margin:1mm 0;">REGISTRO DE CALIFICACION DE SOLDADORES</div>
      <div style="font-size:10pt;font-weight:bold;">(RCS)</div>
      <div style="font-size:8pt;">WELDER PERFORMANCE QUALIFICATIONS (WPQ)</div>
    </td>

    {{-- Right: QR + RCS number --}}
    <td width="28%" style="text-align:center;" class="p2">
      @if($qrDataUri)
        <img src="{{ $qrDataUri }}" style="width:22mm;height:22mm;" /><br>
      @endif
      <div style="font-size:7pt;margin-top:1mm;">RCS / WPQ</div>
      <div class="rcs-number">Nº {{ $rcsNumero }}</div>
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

{{-- WELDER SECTION --}}
<table style="margin-top:1mm;border:0.3mm solid #000;">
  <tr>
    {{-- Welder data --}}
    <td width="72%" style="border-right:0.3mm solid #000;" class="p2">
      <table style="border:none;">
        <tr>
          <td style="border:none;" class="p1">
            <span class="label">Welder's name-<br><strong>Nombre del Soldador</strong><br>Stamp / CUÑO</span>
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
            <span class="label">Company: <strong>EMPRESA:</strong></span><br>
            <span class="value-bold">{{ $empresa ?: 'PARTICULAR' }}</span>
          </td>
          <td width="50%" style="border:none;" class="p1">
            <span class="label">Identification - Nº documento :</span><br>
            <span class="value-bold">{{ $dni }}</span>
          </td>
        </tr>
        <tr>
          <td width="50%" style="border:none;" class="p1">
            <span class="label">Identification of WPS followed:<br><strong>Identificación de EPS Aplicado:</strong></span><br>
            <span class="value-bold">{{ $cert->eps_numero }}</span>
          </td>
          @if($cert->pqr_numero)
          <td width="50%" style="border:none;" class="p1">
            <span class="label">Identification of PQR:<br><strong>Identificación de RCP:</strong></span><br>
            <span class="value-bold">{{ $cert->pqr_numero }}</span>
          </td>
          @endif
        </tr>
        <tr>
          <td width="50%" style="border:none;" class="p1">
            <span class="label">Qualification Date<br><strong>Fecha de Calificación</strong></span><br>
            <span class="value-bold">{{ $fechaCalif }}</span>
          </td>
          <td width="50%" style="border:none;" class="p1">
            <span class="label vto-label">Expiration date<br>FECHA DE VENCIMIENTO</span><br>
            <span class="vto-value">{{ $fechaVto }}</span>
          </td>
        </tr>
        <tr>
          <td colspan="2" style="border:none;" class="p1">
            <span class="label">Extension/Renewal of the RCS / WPQ &nbsp;
              <strong>Ampliación/ Renovación del RCS / WPQ</strong>
              &nbsp; {!! $chk($cert->tipo !== 'inicial') !!}
            </span>
          </td>
        </tr>
      </table>
    </td>

    {{-- DNI Photo --}}
    <td width="28%" class="p2 center middle">
      @if($fotoDataUri)
        <img src="{{ $fotoDataUri }}" style="max-width:42mm;max-height:52mm;" />
      @else
        <div style="border:0.3mm solid #ccc;width:42mm;height:52mm;text-align:center;vertical-align:middle;font-size:7pt;color:#888;">
          <br><br><br>Foto DNI
        </div>
      @endif
    </td>
  </tr>
</table>

{{-- COUPON SECTION --}}
<table style="margin-top:1mm;border:0.3mm solid #000;">
  <tr>
    <td width="22%" class="b p1">
      <span class="label">Test coupon /<br><strong>Cupón de prueba</strong></span><br>
      <div style="margin-top:1mm;font-size:7pt;">{!! $chk($esCanio) !!} Pipe / Caño</div>
      <div style="font-size:7pt;">{!! $chk($esChapa) !!} Sheet / Chapa</div>
    </td>
    <td width="22%" class="b p1">
      <span class="label">Production Weld<br><strong>Soldadura de producción</strong></span>
    </td>
    <td width="56%" class="b p1">
      <span class="label">Specification of base metal(s)<br>
        <strong>Especificación del Metal(es) Base:</strong>
      </span>
      <span class="value-bold"> {{ $metalBase }}</span><br>
      <div style="margin-top:1mm;font-size:7.5pt;">
        {{ $couponDisplay }}&nbsp;&nbsp;
        <strong>{{ $rangoCalDisplay }}</strong>
      </div>
    </td>
  </tr>
</table>

{{-- CERTIFICATION TEXT --}}
<div style="margin-top:2mm;" class="cert-text">
  We certify that the statements in this record are correct and that the test coupons were prepared,
  welded, and tested in accordance with the requirements of {{ $certTextEn }}<br>
  Nosotros certificamos que las declaraciones en este registro son correctas y que los cupones de la
  prueba fueron preparados, soldados y ensayados de acuerdo a los requisitos de {{ $certTextEs }}
</div>
<div class="verify-text" style="margin-top:1mm;">
  VERIFICAR LA AUTENTICIDAD DE ESTE DOCUMENTO ESCANEANDO EL CODIGO QR
</div>
<div class="label right" style="margin-top:0.5mm;">Página 1 de 2</div>

{{-- VARIABLES TABLE --}}
<table style="margin-top:2mm;border:0.3mm solid #000;">
  <thead>
    <tr style="background:#e8e8e8;">
      <th width="32%" class="b p1 center">
        <span class="label-bold">Welding Variables</span><br>
        <span class="label-bold">Variables de Soldadura{{ $esAsme ? ' (QW-350)' : '' }}</span>
      </th>
      <th width="30%" class="b p1 center">
        <span class="label-bold">Testing Conditions</span><br>
        <span class="label-bold">Condiciones de Ensayo</span>
      </th>
      <th width="28%" class="b p1 center">
        <span class="label-bold">Range Qualified</span><br>
        <span class="label-bold">Rangos Calificados</span>
      </th>
      <th width="10%" class="b p1 center">
        <span class="label-bold">Comments<br>/Comentarios</span>
      </th>
    </tr>
  </thead>
  <tbody>
    {{-- Tipo / Type --}}
    <tr>
      <td class="b p1">
        <span class="label">Type (ej; manual, semi-auto) used<br>
        <strong>Tipo Usado(ej; manual, semi-auto)</strong></span>
      </td>
      <td class="b p1 center middle"><span class="bold">{{ $tipoUso }}</span></td>
      <td class="b p1 center middle"><span class="bold">{{ $tipoUso }}</span></td>
      <td class="b p1"></td>
    </tr>

    {{-- Backing / Respaldo --}}
    <tr>
      <td class="b p1">
        <span class="label">Backing (metal, weld metal, double-welded,etc<br>
        <strong>Respaldo</strong> (metal. soldadura, doble soldadura, etc)</span>
      </td>
      <td class="b p1 center middle"><span class="bold">{{ $respaldo }}</span></td>
      <td class="b p1 center middle"><span class="bold">{{ $respaldoRng }}</span></td>
      <td class="b p1"></td>
    </tr>

    {{-- P-Number / Metal base --}}
    <tr>
      <td class="b p1">
        <span class="label">Base metal P or S-Number to P- or S-Number<br>
        <strong>Metal Base Número P ó S a Número P ó S</strong></span>
      </td>
      <td class="b p1 center middle"><span class="bold">{{ $pNumber }}</span></td>
      <td class="b p1 middle"><span style="font-size:6.5pt;">{{ $pNumRng }}</span></td>
      <td class="b p1"></td>
    </tr>

    {{-- Electrode / Filler --}}
    <tr>
      <td class="b p1">
        <span class="label">Filler metal or electrode classification(s) AWS<br>
        <strong>Metal de aporte o clasificación del electrodo</strong></span>
      </td>
      <td class="b p1 center middle" colspan="2"><span class="bold">{{ $electrodo }}</span></td>
      <td class="b p1"></td>
    </tr>

    {{-- F-Number --}}
    <tr>
      <td class="b p1">
        <span class="label">Filler metal F- Number(s)<br>
        <strong>Metal de aporte Número(s) F:</strong></span>
      </td>
      <td class="b p1 center middle"><span class="bold">{{ $fNumber }}</span></td>
      <td class="b p1 middle"><span style="font-size:6.5pt;">{{ $fNumRng }}</span></td>
      <td class="b p1 center middle"><span class="bold">{{ $aNumber }}</span></td>
    </tr>

    {{-- Tungsteno GTAW --}}
    <tr>
      <td class="b p1"><span class="label">Tungsteno GTAW</span></td>
      <td class="b p1 center middle"><span class="bold">{{ $tungsteno }}</span></td>
      <td class="b p1 center middle"><span class="bold">{{ $tungsteno }}</span></td>
      <td class="b p1"></td>
    </tr>

    {{-- Diseño de junta --}}
    <tr>
      <td class="b p1">
        <span class="label-bold">DISEÑO DE JUNTA</span>
      </td>
      <td class="b p1">
        @if($svg)
          <div style="width:55mm;height:35mm;overflow:hidden;">{!! $svg !!}</div>
        @endif
        <div class="bold" style="font-size:7.5pt;margin-top:1mm;">{{ $juntaNombre }}</div>
        @if($juntaDetalle)
          <div style="font-size:7pt;">{{ $juntaDetalle }}</div>
        @endif
      </td>
      <td class="b p1 middle">
        @if($juntaRng)<span class="bold" style="font-size:7.5pt;">{{ $juntaRng }}</span>@endif
      </td>
      <td class="b p1"></td>
    </tr>

    {{-- Deposit thickness --}}
    <tr>
      <td class="b p1">
        <span class="label">Deposit thickness for each process<br>
        <strong>Espesor depositado para cada proceso</strong></span>
      </td>
      <td class="b p1 center middle">
        <span class="bold">{{ $espDep ? $espDep . ' mm' : '' }}</span>
      </td>
      <td class="b p1 middle">
        @if($espMaxCal)
          <span class="label">Max Qualified Thickness<br>
          <strong>Espesor max Calificado:</strong> CALIFICA: {{ $espMaxCal }}</span>
        @endif
      </td>
      <td class="b p1"></td>
    </tr>

    {{-- Position / Progression --}}
    <tr>
      <td class="b p1">
        <span class="label">Position/Progression (uphill or downhill):<br>
        <strong>Posición / Progresión (ascendente descendente)</strong></span>
      </td>
      <td class="b p1 center middle"><span class="bold">{{ $posTest }}</span></td>
      <td class="b p1 center middle">
        <span class="bold" style="font-size:7pt;">{{ $posRngText }}</span>
      </td>
      <td class="b p1"></td>
    </tr>

    {{-- Gas --}}
    <tr>
      <td class="b p1">
        <span class="label">Type of gas/Tipo de gas/Caudal (GTAW)</span>
      </td>
      <td class="b p1 center middle"><span class="bold">{{ $gas }}</span></td>
      <td class="b p1 center middle"><span class="bold">{{ $gas }}</span></td>
      <td class="b p1"></td>
    </tr>

    {{-- Inert gas backing --}}
    <tr>
      <td class="b p1">
        <span class="label">Inert gas backing /<br>
        <strong>Respaldo de gas inerte</strong> (GTAW) / Caudal</span>
      </td>
      <td class="b p1 center middle"><span class="bold">{{ $gasResp }}</span></td>
      <td class="b p1 center middle"><span class="bold">{{ $gasResp }}</span></td>
      <td class="b p1"></td>
    </tr>

    {{-- Current / Polarity --}}
    <tr>
      <td class="b p1">
        <span class="label">Current type/polarity-<br>
        <strong>Tipo de corriente/polaridad</strong></span>
      </td>
      <td class="b p1 center middle"><span class="bold">{{ $corriente }}</span></td>
      <td class="b p1"></td>
      <td class="b p1"></td>
    </tr>
  </tbody>
</table>

{{-- ══════════════════════════════════════════════════════ PÁGINA 2 ══ --}}
<pagebreak />

{{-- HEADER (repeated) --}}
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
      @if($normaLogoDataUri)
        <img src="{{ $normaLogoDataUri }}" style="height:10mm;max-width:30mm;" /><br>
      @endif
      <div style="font-size:8pt;font-style:italic;">{{ $normaEdicion }}</div>
      <div style="font-size:11pt;font-weight:bold;margin:1mm 0;">REGISTRO DE CALIFICACION DE SOLDADORES</div>
      <div style="font-size:10pt;font-weight:bold;">(RCS)</div>
      <div style="font-size:8pt;">WELDER PERFORMANCE QUALIFICATIONS (WPQ)</div>
    </td>
    <td width="28%" style="text-align:center;" class="p2">
      @if($qrDataUri)
        <img src="{{ $qrDataUri }}" style="width:22mm;height:22mm;" /><br>
      @endif
      <div style="font-size:7pt;margin-top:1mm;">RCS / WPQ</div>
      <div class="rcs-number">Nº {{ $rcsNumero }}</div>
    </td>
  </tr>
</table>

{{-- Process row --}}
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

{{-- Welder + coupon recap (mini) --}}
<table style="margin-top:1mm;border:0.3mm solid #000;">
  <tr>
    <td width="72%" style="border-right:0.3mm solid #000;" class="p1">
      <table style="border:none;">
        <tr>
          <td style="border:none;width:30%;" class="p1">
            <span class="label">Welder's name<br><strong>Nombre del Soldador</strong><br>Stamp / CUÑO</span>
          </td>
          <td style="border:none;" class="p1">
            <div class="welder-name">{{ $apellido }}</div>
            <div class="welder-name">{{ $nombre }}</div>
          </td>
        </tr>
        <tr>
          <td style="border:none;" class="p1"><span class="label">Company: <strong>EMPRESA:</strong></span></td>
          <td style="border:none;" class="p1 bold">{{ $empresa ?: 'PARTICULAR' }}</td>
        </tr>
        <tr>
          <td style="border:none;" class="p1"><span class="label">Identification - Nº documento :</span></td>
          <td style="border:none;" class="p1 bold">{{ $dni }}</td>
        </tr>
        <tr>
          <td style="border:none;" class="p1"><span class="label"><strong>Identificación de EPS:</strong></span></td>
          <td style="border:none;" class="p1 bold">{{ $cert->eps_numero }}</td>
        </tr>
        <tr>
          <td style="border:none;" class="p1"><span class="label">Qualification Date / <strong>Fecha de Calificación</strong></span></td>
          <td style="border:none;" class="p1 bold">{{ $fechaCalif }}</td>
        </tr>
        <tr>
          <td style="border:none;" class="p1 vto-label">Expiration date / FECHA DE VENCIMIENTO</td>
          <td style="border:none;" class="p1 vto-value">{{ $fechaVto }}</td>
        </tr>
      </table>
    </td>
    <td width="28%" class="p1">
      <table style="border:none;">
        <tr>
          <td style="border:none;" class="p1">
            <span class="label">Test coupon / <strong>Cupón de prueba</strong></span><br>
            <div style="font-size:7pt;">{!! $chk(!$esCanio) !!} Pipe / Caño</div>
            <div style="font-size:7pt;">{!! $chk($esChapa) !!} Sheet / Chapa</div>
          </td>
        </tr>
        <tr>
          <td style="border:none;" class="p1">
            <span class="label">Especificación Metal Base:</span><br>
            <span class="bold">{{ $metalBase }}</span><br>
            <span style="font-size:7pt;">{{ $couponDisplay }}</span><br>
            <span style="font-size:7pt;">{{ $rangoCalDisplay }}</span>
          </td>
        </tr>
      </table>
    </td>
  </tr>
</table>

{{-- ── RESULTS --}}
<div class="section-title" style="margin-top:3mm;">
  <em>RESULTS / RESULTADOS</em>
</div>

<div style="margin-top:2mm;">

  {{-- Visual Examination --}}
  <div class="result-line">
    {!! $chk($aprobado('VT')) !!}
    <span class="label">Visual Examination of Completed Weld/<strong>Examen visual de la soldadura completa{{ $esAsme ? ' (QW-302.4)' : '' }}:</strong></span>
    @if($tr('VT')) <span class="bold">{{ strtoupper($tr('VT')) }}</span> @endif
  </div>

  {{-- Bend test --}}
  <div class="result-line">
    {!! $chk($aprobado('BT-R') || $aprobado('BT-F')) !!}
    <span class="label">Bend test/<strong>Prueba de plegado:</strong></span>
    @if($tr('BT-R') || $tr('BT-F'))
      <span class="bold">{{ strtoupper($tr('BT-R') ?: $tr('BT-F')) }}</span>
    @endif
    &nbsp;&nbsp;&nbsp;
    {!! $chk($aprobado('BT-R')) !!}
    <span class="label">Transverse root and face/<strong>Transversal de raíz y cara{{ $esAsme ? ' [QW-462.3(a)]' : '' }}:</strong></span>
    @if($tr('BT-R')) <span class="bold">{{ strtoupper($tr('BT-R')) }}</span> @endif
  </div>

  <div class="result-line" style="margin-left:5mm;">
    {!! $chk(false) !!}
    <span class="label">Longitudinal root and face/<strong>Longitudinal de raíz y cara{{ $esAsme ? ' [QW-462.3(b)]' : '' }}</strong></span>
    &nbsp;&nbsp;&nbsp;&nbsp;
    {!! $chk($aprobado('BT-S')) !!}
    <span class="label">Side/<strong>Lateral{{ $esAsme ? ' (QW-462.2)' : '' }}</strong></span>
    @if($tr('BT-S')) <span class="bold">{{ strtoupper($tr('BT-S')) }}</span> @endif
  </div>

  <div class="result-line" style="margin-left:5mm;">
    {!! $chk(false) !!}
    <span class="label">Pipe bend specimen/corrosión - resistant overlay{{ $esAsme ? ' [QW-462.5(e)]' : '' }}</span>
    &nbsp;&nbsp;&nbsp;&nbsp;
    {!! $chk(false) !!}
    <span class="label">Plate bend specimen/corrosion - resistant overlay{{ $esAsme ? ' [QW-462.5(d)]' : '' }}</span>
  </div>

  {{-- Radiographic --}}
  <div class="result-line">
    {!! $chk($aprobado('RT')) !!}
    <span class="label">Alternative radiographic examination results/<strong>Resultado de la alternativa de radiografiado{{ $esAsme ? ' (QW-191)' : '' }}:</strong></span>
    <span class="bold">{{ $naResult('RT') ? 'NA' : strtoupper($tr('RT')) }}</span>
  </div>

  {{-- Fillet fracture --}}
  <div class="result-line">
    {!! $chk(false) !!}
    <span class="label">Fillet weld - fracture test/<strong>Soldadura de filete-ensayo de fractura{{ $esAsme ? ' (QW-180)' : '' }}:</strong></span>
    <span class="label">Length and percent of defects/<strong>Longitud y porcentaje de los defectos:</strong></span>
  </div>

  {{-- Macro --}}
  <div class="result-line">
    {!! $chk(false) !!}
    <span class="label">Macro examination / <strong>Macro examen{{ $esAsme ? ' (QW-183)' : '' }}:</strong></span>
    &nbsp;&nbsp;&nbsp;
    <span class="label">Fillet size/<strong>Tamaño del filete:</strong></span>
    &nbsp;&nbsp;&nbsp;
    <span class="label">Convexity Concavity/<strong>Concavidad-convexidad:</strong></span>
  </div>

</div>

{{-- TABLE 6.10 (AWS D1.1 only — positions matrix) --}}
@if($esAws && $posRangs)
<div style="margin-top:3mm;border:0.3mm solid #000;padding:2mm;">
  <div style="font-size:7pt;font-weight:bold;margin-bottom:1mm;">
    Posiciones calificadas ({{ $norma }})
  </div>
  <div style="font-size:7pt;">{{ $posRangs }}</div>
</div>
@endif

{{-- NOTAS --}}
<div style="margin-top:3mm;">
  <span class="label-bold">NOTAS:</span>
  <div style="font-size:7pt;margin-top:1mm;">
    1. El Cliente NO solicitó presenciar los ensayos –
       The Client did NOT request to witness the rehearsals<br>
    2. Las probetas ensayadas serán mantenidas en custodia durante 72 Hs, cumplido ese plazo se descartarán.
       The tested specimens will be kept in custody for 72 hours, after which period they will be discarded.
  </div>
  @if($cert->observaciones)
    <div style="font-size:7pt;margin-top:1mm;">{{ $cert->observaciones }}</div>
  @endif
</div>

{{-- RESULT STAMP --}}
@if(strtolower($cert->resultado) === 'aprobado')
<div style="margin-top:4mm;text-align:center;">
  <div class="aprobado-stamp">SOLDADOR APROBADO</div>
  <div style="font-size:9pt;">APPROVED WELDER</div>
</div>
@else
<div style="margin-top:4mm;text-align:center;">
  <div style="font-size:12pt;font-weight:bold;color:#CC0000;">SOLDADOR RECHAZADO</div>
  <div style="font-size:9pt;color:#CC0000;">FAILED WELDER</div>
</div>
@endif

{{-- SIGNATURE --}}
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

{{-- CERTIFICATION TEXT (page 2) --}}
<div style="margin-top:3mm;" class="cert-text">
  We certify that the statements in this record are correct and that the test coupons were prepared,
  welded, and tested in accordance with the requirements of {{ $certTextEn }}<br>
  Nosotros certificamos que las declaraciones en este registro son correctas y que los cupones de la
  prueba fueron preparados, soldados y ensayados de acuerdo a los requisitos de {{ $certTextEs }}
</div>
<div class="verify-text" style="margin-top:1mm;">
  VERIFICAR LA AUTENTICIDAD DE ESTE DOCUMENTO ESCANEANDO EL CODIGO QR
</div>
<div class="label right" style="margin-top:0.5mm;">Página 2 de 2</div>

</body>
</html>
