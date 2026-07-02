@php
// ── Helpers ──────────────────────────────────────────────────────────────────
$vars = $cert->variables ?? [];
$v    = fn($k, $d = '') => $vars[$k] ?? $d;

// Test results
$tr       = fn($code) => $cert->tests->first(fn($t) => optional($t->testType)->code === $code)?->resultado ?? '';
$aprobado = fn($code) => strtolower($tr($code)) === 'aprobado';

// Ranges
$rango    = fn($type) => $cert->ranges->firstWhere('type', $type)?->descripcion ?? '';

// Date formatting
$meses = [1=>'ENERO',2=>'FEBRERO',3=>'MARZO',4=>'ABRIL',5=>'MAYO',6=>'JUNIO',
          7=>'JULIO',8=>'AGOSTO',9=>'SEPTIEMBRE',10=>'OCTUBRE',11=>'NOVIEMBRE',12=>'DICIEMBRE'];
$fechaCalif = $cert->fecha_calificacion
    ? sprintf('%02d/%s/%d',
        $cert->fecha_calificacion->day,
        $meses[$cert->fecha_calificacion->month],
        $cert->fecha_calificacion->year)
    : '';
$fechaVto = $cert->fecha_vencimiento?->format('d/\M\E\S/Y') ?? '';
$fechaVtoDisplay = $cert->fecha_vencimiento
    ? sprintf('%02d/%s/%d',
        $cert->fecha_vencimiento->day,
        $meses[$cert->fecha_vencimiento->month],
        $cert->fecha_vencimiento->year)
    : '';

// Variables de soldadura
$proceso       = strtoupper($cert->proceso ?? '');
$espCupon      = $v('espesor_cupon', '');
$diamCupon     = $v('diametro_cupon', '');
$metalBase      = strtoupper($v('metal_base', $v('especificacion_metal_base', '')));
$respaldo       = strtoupper($v('respaldo', ''));
$backingMat     = strtoupper($v('backing_material', ''));
$lineTipo       = strtoupper($v('linea_tipo', 'LINEA REGULAR'));
$grupoElect    = strtoupper($v('grupo_consumible', ''));
$electRaiz     = strtoupper($v('electrodo_raiz', ''));
$electRelleno  = strtoupper($v('electrodo_relleno', ''));
$electTerm     = strtoupper($v('electrodo_terminacion', ''));
$corrRaiz      = strtoupper($v('corriente_raiz', ''));
$corrRelleno   = strtoupper($v('corriente_relleno', ''));
$preheat       = $v('temperatura_preheat', '');
$tempInterpass = $v('temperatura_interpass', '');
$progresion    = strtoupper($cert->progresion ?? '');
$posicion      = strtoupper($cert->posicion ?? '');
$gasProteccion = strtoupper($v('gas_proteccion', 'N / A')) ?: 'N / A';
$numPasadas    = $v('num_pasadas', '1');
$velAvance     = $v('velocidad_avance', 'Ver Tabla');
$tiempoP1P2    = $v('tiempo_p1_p2', '');
$tiempoP2rest  = $v('tiempo_p2_rest', '');
$limpieza      = strtoupper($v('limpieza_entre_pasadas', 'CEPILLO MANUAL O MECÁNICO'));
$presentador   = strtoupper($v('presentador', 'EXTERNO'));
$ciudad        = $cert->soldador?->ciudad ?? '';

// Joint design
$svgRaw = $cert->jointDesign?->svg ?? '';
$svg = preg_replace('/(<svg[^>]*?)(\s+width="[^"]*"|\s+height="[^"]*")/i', '$1', $svgRaw);
$juntaNombre = strtoupper($cert->jointDesign?->nombre ?? 'En "V" a Tope');

// Material base ranges (API 1104 shows tensile specs)
$matBaseRngDe  = $v('material_base_rango_desde', '');
$matBaseRngA   = $v('material_base_rango_hasta', '');
$matAsmeEq     = $v('material_asme_equivalente', '');
$diamCalif        = $rango('diametro');
$espCalif         = $rango('espesor');
$grupoBaseRng     = $rango('grupo_base_metal');
$grupoElectRng    = $rango('grupo_consumible') ?: $grupoElect;

// Welder
$apellido = strtoupper($cert->soldador?->apellido ?? '');
$nombre   = strtoupper($cert->soldador?->nombre ?? '');
$dni      = $cert->soldador?->dni ?? '';
$cuno     = $cert->soldador?->cuño ?? '';
$empresa  = $cert->empresa?->nombre ?? '';

// Inspector
$inspNombre = $cert->inspector?->nombre ?? '';
$inspCert   = $cert->inspector?->certificacion ?? '';
$inspTel    = $cert->inspector?->telefono ?? '';
$inspEmail  = $cert->inspector?->email ?? '';

// RCS Code
$rcsNumero = $cert->numero . '-' . substr((string)$cert->anio, -2) . '-Rev. ' . $cert->revision;

// Checkbox helper
$chk = fn(bool $c) => $c ? '&#9632;' : '&#9633;';

// Passes (API 1104 pass-by-pass table)
$passes = $cert->passes ?? collect();
@endphp
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<style>
* { box-sizing: border-box; }
body { font-family: Arial, sans-serif; font-size: 8.5pt; color: #000; margin: 0; padding: 0; }
table { border-collapse: collapse; width: 100%; }
td, th { vertical-align: top; }
.b { border: 0.3mm solid #000; }
.bold { font-weight: bold; }
.center { text-align: center; }
.right { text-align: right; }
.middle { vertical-align: middle; }
.p2 { padding: 1.5mm 2mm; }
.p1 { padding: 1mm 1.5mm; }
.label { font-size: 7pt; }
.field-row { margin: 1.5mm 0; }
.field-label { font-size: 7pt; }
.field-value { font-size: 9pt; font-weight: bold; }
.rcs-number { font-size: 9pt; font-weight: bold; }
.proceso-title { font-size: 20pt; font-weight: bold; text-align: center; }
.date-highlight { font-size: 10pt; font-weight: bold; color: #CC0000; text-align: center; }
.aprobado-stamp { font-size: 12pt; font-weight: bold; text-align: center; }
.cert-text { font-size: 6.5pt; text-align: justify; }
.verify-text { font-size: 7pt; font-weight: bold; color: #CC0000; text-align: center; }
.law-text { font-size: 6.5pt; color: #CC0000; text-align: center; }
.passes-th { background: #222; color: #fff; font-size: 7pt; font-weight: bold; text-align: center; padding: 1mm; }
.passes-td { font-size: 7.5pt; text-align: center; padding: 1mm; }
.section-title { font-size: 9pt; font-weight: bold; text-decoration: underline; text-align: center; }
.result-line { font-size: 7.5pt; margin: 1.5mm 0; }
</style>
</head>
<body>

{{-- ═══════════════════════════════════════════════════════ PÁGINA 1 ══ --}}

{{-- HEADER --}}
<table style="border:0.3mm solid #000;">
  <tr>
    {{-- Left: Inspector --}}
    <td width="28%" style="border-right:0.3mm solid #000;" class="p2">
      @if($kosoldarLogoDataUri)
        <img src="{{ $kosoldarLogoDataUri }}" style="height:12mm;max-width:35mm;" /><br>
      @endif
      <span class="bold" style="font-size:7.5pt;">{{ $inspNombre }}</span><br>
      <span class="label">Móvil {{ $inspTel }}</span><br>
      <span class="label">{{ $inspEmail }}</span>
    </td>

    {{-- Center: API logo + title --}}
    <td width="44%" style="border-right:0.3mm solid #000;text-align:center;" class="p2 middle">
      @if($normaLogoDataUri)
        <img src="{{ $normaLogoDataUri }}" style="height:12mm;max-width:40mm;" /><br>
      @endif
      <div style="font-size:9pt;font-weight:bold;">API STANDARD 1104</div>
      <div style="font-size:8pt;font-style:italic;">Ed 2022</div>
      <div style="font-size:10pt;font-weight:bold;margin:1mm 0;">REGISTRO DE CALIFICACION DE SOLDADORES (RCS)</div>
      <div style="font-size:8pt;">WELDER PERFORMANCE QUALIFICATIONS (WPQ)</div>
      <div class="proceso-title" style="margin-top:2mm;">{{ $proceso }}</div>
    </td>

    {{-- Right: QR + Number --}}
    <td width="28%" style="text-align:center;" class="p2">
      @if($qrDataUri)
        <img src="{{ $qrDataUri }}" style="width:22mm;height:22mm;" /><br>
      @endif
      <div style="font-size:7pt;margin-top:1mm;">Nº {{ $rcsNumero }}</div>
    </td>
  </tr>
</table>

{{-- WELDER + COUPON --}}
<table style="margin-top:1.5mm;border:0.3mm solid #000;">
  <tr>
    {{-- Welder name + stamp --}}
    <td width="45%" class="b p2">
      <span class="label">Welder's name-<strong>Nombre del Soldador</strong><br>Stamp / CUÑO</span><br>
      <span style="font-size:13pt;font-weight:bold;">{{ $apellido }} {{ $nombre }}</span><br>
      @if($cuno)<span style="font-size:8pt;">{{ $cuno }}</span>@endif
    </td>
    {{-- DNI --}}
    <td width="25%" class="b p2">
      <span class="label">Identification - <strong>Nº documento</strong></span><br>
      <span class="field-value">{{ $dni }}</span>
    </td>
    {{-- Company --}}
    <td width="30%" class="b p2">
      <span class="label">Company: <strong>EMPRESA:</strong></span><br>
      <span style="font-size:10pt;font-weight:bold;">{{ $empresa ?: 'PARTICULAR' }}</span><br>
      @if($ciudad)
        <span class="label">City: <strong>CIUDAD:</strong> {{ $ciudad }}</span>
      @endif
    </td>
  </tr>
  <tr>
    {{-- Test coupon --}}
    <td class="b p2">
      <span class="label">Test coupon / <strong>Cupón de prueba</strong></span>
      <span style="font-size:8pt;">
        {!! $chk(true) !!} Pipe / Caño &nbsp;
        {!! $chk(false) !!} sheet / Chapa
      </span>
      &nbsp;&nbsp;
      <span class="label">Quality / <strong>Calidad</strong></span><br>
      <span class="field-value">{{ $metalBase }}</span>
    </td>
    {{-- Production weld --}}
    <td class="b p2">
      <span class="label">Production Weld / <strong>Soldadura de producción</strong></span>
      {!! $chk(false) !!}
    </td>
    {{-- Dates --}}
    <td class="b p2">
      <span class="label">Qualification Date<br><strong>Fecha de Calificación</strong></span><br>
      <span class="field-value">{{ $fechaCalif }}</span>
    </td>
  </tr>
  <tr>
    {{-- WPS --}}
    <td class="b p2">
      <span class="label">Identification of WPS followed:<br><strong>Identificación de EPS Aplicado:</strong></span><br>
      <span class="field-value">{{ $cert->eps_numero }}</span>
    </td>
    {{-- PQR --}}
    <td class="b p2">
      <span class="label">Identification of PQR followed:<br><strong>Identificación de RCP:</strong></span><br>
      <span class="field-value">{{ $cert->pqr_numero }}</span>
    </td>
    <td class="b p2"></td>
  </tr>
</table>

{{-- PROCESS SPECIFICATIONS (linear layout, no grid) --}}
<table style="margin-top:2mm;border:0.3mm solid #000;">
  <tr>
    <td class="p2">

      <div class="field-row">
        <span class="field-label">Proceso de soldadura: </span>
        <span class="field-value">{{ $proceso }} - electrodo revestido</span>
      </div>

      @if($matBaseRngDe || $matBaseRngA)
      <div class="field-row">
        <span class="field-label">Materiales Base: &nbsp; Calificados</span><br>
        @if($matBaseRngDe)
          <span class="field-label">&nbsp;&nbsp;&nbsp;DE &nbsp;&nbsp; ≥ {{ $matBaseRngDe }}</span><br>
        @endif
        @if($matBaseRngA)
          <span class="field-label">&nbsp;&nbsp;&nbsp;A &nbsp;&nbsp;&nbsp;&nbsp; ≤ {{ $matBaseRngA }}</span><br>
        @endif
        @if($matAsmeEq)
          <span class="field-label">&nbsp;&nbsp;&nbsp;MATERIALES ASME IX: {{ $matAsmeEq }}</span>
        @endif
      </div>
      @else
      <div class="field-row">
        <span class="field-label">Material base: </span>
        <span class="field-value">{{ $metalBase }}</span>
      </div>
      @endif

      @if($grupoBaseRng)
      <div class="field-row">
        <span class="field-label">Grupos calificados (metal base): </span>
        <span class="field-value" style="font-size:7pt;">{{ $grupoBaseRng }}</span>
      </div>
      @endif

      @if($diamCalif)
      <div class="field-row">
        <span class="field-label">Diámetro calificado &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>
        <span class="field-value">{{ $diamCalif }}</span>
      </div>
      @endif

      @if($espCalif)
      <div class="field-row">
        <span class="field-label">Espesor de la cañería [mm] &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>
        <span class="field-value">{{ $espCalif }}</span>
      </div>
      @endif

      @if($respaldo)
      <div class="field-row">
        <span class="field-label">Respaldo: &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>
        <span class="field-value">{{ $respaldo }}@if($backingMat) — {{ $backingMat }}@endif</span>
      </div>
      @endif

      <div class="field-row">
        <span class="field-label">Diseño de junta: &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>
        <span class="field-value">{{ $juntaNombre }}</span>
      </div>

      @if($electRaiz || $electRelleno)
      <div class="field-row">
        <span class="field-label">Material de aporte: &nbsp;&nbsp;&nbsp;&nbsp;
          <strong>Raíz:</strong> {{ $grupoElect ? $grupoElect . ' ' : '' }}{{ $electRaiz }}
          @if($electRelleno)
            &nbsp;-&nbsp; <strong>Relleno/Terminación:</strong> {{ $electRelleno }}
          @endif
          @if($electTerm && $electTerm !== $electRelleno)
            &nbsp;/&nbsp; {{ $electTerm }}
          @endif
        </span>
      </div>
      @if($grupoElectRng && $grupoElectRng !== $grupoElect)
      <div class="field-row">
        <span class="field-label">Grupos consumible calificados: </span>
        <span class="field-value" style="font-size:7pt;">{{ $grupoElectRng }}</span>
      </div>
      @endif
      @endif

      <div class="field-row">
        <span class="field-label">Características eléctricas: &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>
        <span class="field-value">Ver hoja 2</span>
      </div>

      <div class="field-row">
        <span class="field-label">Características de la llama: &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>
        <span class="field-value">NA</span>
      </div>

      <div class="field-row">
        <span class="field-label">Posición: &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>
        <span class="field-value">{{ $posicion }}{{ $progresion ? ', eje ' . $progresion : '' }}</span>
      </div>

      <div class="field-row">
        <span class="field-label">Progresión de la soldadura: &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>
        <span class="field-value">{{ $progresion }}</span>
      </div>

      <div class="field-row">
        <span class="field-label">Número de soldadores: &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>
        <span class="field-value">1</span>
      </div>

      @if($tiempoP1P2)
      <div class="field-row">
        <span class="field-label">Tiempo máximo entre 1ª y 2ª pasadas: &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>
        <span class="field-value">{{ $tiempoP1P2 }}</span>
      </div>
      @endif

      @if($tiempoP2rest)
      <div class="field-row">
        <span class="field-label">Tiempo máximo entre 2ª pasada y restantes: &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>
        <span class="field-value">{{ $tiempoP2rest }}</span>
      </div>
      @endif

      <div class="field-row">
        <span class="field-label">Limpieza: &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>
        <span class="field-value">{{ $limpieza }}</span>
      </div>

      <div class="field-row">
        <span class="field-label">Precalentamiento [ ºC ]: </span>
        <span class="field-value">{{ $preheat ?: '—' }}</span>
        &nbsp;&nbsp;&nbsp;&nbsp;
        <span class="field-label">Máxima temperatura entre pasadas [ ºC ]: </span>
        <span class="field-value">{{ $tempInterpass ?: '—' }}</span>
      </div>

      <div class="field-row">
        <span class="field-label">Gas Protector (tipo y caudal): &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>
        <span class="field-value">{{ $gasProteccion }}</span>
      </div>

      <div class="field-row">
        <span class="field-label">Presentador: &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>
        <span class="field-value">{{ $presentador }}</span>
        &nbsp;&nbsp; {!! $chk(strtolower($presentador) === 'externo') !!}
      </div>

    </td>
  </tr>
</table>

{{-- DATE HIGHLIGHT + CERTIFICATION TEXT --}}
<div class="date-highlight" style="margin-top:3mm;">
  Date/Fecha: <strong>{{ $fechaCalif }}</strong>
  &nbsp;&nbsp;&nbsp;
  Fecha de Vto. <strong>{{ $fechaVtoDisplay }}</strong>
</div>

<div style="margin-top:2mm;" class="cert-text">
  We certify that the statements in this record are correct and that the test coupons were prepared,
  welded, and tested in accordance with the requirements of API STANDARD 1104 Ed 2022<br>
  Nosotros certificamos que las declaraciones en este registro son correctas y que los cupones de la
  prueba fueron preparados, soldados y ensayados de acuerdo a los requisitos de API STANDARD 1104 Ed 2022
</div>
<div class="verify-text" style="margin-top:1mm;">
  VERIFICAR LA AUTENTICIDAD DE ESTE DOCUMENTO ESCANEANDO EL CODIGO QR
</div>
<div class="label right" style="margin-top:0.5mm;">Página 1 de 2</div>

{{-- ═══════════════════════════════════════════════════════ PÁGINA 2 ══ --}}
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
        <img src="{{ $normaLogoDataUri }}" style="height:12mm;max-width:40mm;" /><br>
      @endif
      <div style="font-size:9pt;font-weight:bold;">API STANDARD 1104 &nbsp; <em>Ed 2022</em></div>
      <div style="font-size:10pt;font-weight:bold;margin:1mm 0;">REGISTRO DE CALIFICACION DE SOLDADORES (RCS)</div>
      <div style="font-size:8pt;">WELDER PERFORMANCE QUALIFICATIONS (WPQ)</div>
      <div class="proceso-title" style="margin-top:1mm;">{{ $proceso }}</div>
    </td>
    <td width="28%" style="text-align:center;" class="p2">
      @if($qrDataUri)
        <img src="{{ $qrDataUri }}" style="width:22mm;height:22mm;" /><br>
      @endif
      <div style="font-size:7pt;margin-top:1mm;">Nº {{ $rcsNumero }}</div>
    </td>
  </tr>
</table>

{{-- Limpieza entre pasadas --}}
<div style="border:0.3mm solid #000;padding:1.5mm 2mm;margin-top:2mm;font-size:8pt;">
  <strong>Velocidad de avance [cm/min]:</strong> {{ $velAvance }}
</div>
<div style="border:0.3mm solid #000;border-top:none;padding:1.5mm 2mm;font-size:8pt;">
  <strong>Limpieza entre pasadas:</strong> {{ $limpieza }}
</div>

{{-- VARIABLES DE SOLDADURA (pass-by-pass table) --}}
<div style="border:0.3mm solid #000;border-top:none;padding:0;margin-bottom:2mm;">
  <div style="background:#222;color:#fff;font-size:8.5pt;font-weight:bold;text-align:center;padding:1.5mm;">
    VARIABLES DE SOLDADURA
  </div>
  <table>
    <thead>
      <tr>
        <th class="b passes-th" width="18%">Secuencia:</th>
        <th class="b passes-th" width="10%">Proceso:</th>
        <th class="b passes-th" width="18%">Clasific. AWS:</th>
        <th class="b passes-th" width="14%">Diámetro [mm]:</th>
        <th class="b passes-th" width="10%">Amperaje [A]:</th>
        <th class="b passes-th" width="10%">Voltaje [V]:</th>
        <th class="b passes-th" width="10%">Avance [cm/min]</th>
        <th class="b passes-th" width="10%">Polaridad:</th>
        <th class="b passes-th" width="10%">Progresión:</th>
      </tr>
    </thead>
    <tbody>
      @forelse($passes as $pass)
      <tr>
        <td class="b passes-td">{{ $pass->etiqueta ?? ('Pasada ' . $pass->orden) }}</td>
        <td class="b passes-td">{{ optional($pass->proceso)->nombre ?? '' }}</td>
        <td class="b passes-td">{{ $pass->clasificacion_aporte ?? '' }}</td>
        <td class="b passes-td">{{ $pass->diametro_aporte_mm ?? '' }}</td>
        <td class="b passes-td">
          {{ $pass->amperaje_min ?? '' }}{{ $pass->amperaje_max ? '- ' . $pass->amperaje_max : '' }}
        </td>
        <td class="b passes-td">
          {{ $pass->voltaje_min ?? '' }}{{ $pass->voltaje_max ? '-' . $pass->voltaje_max : '' }}
        </td>
        <td class="b passes-td">
          {{ $pass->velocidad_avance_min ?? '' }}{{ $pass->velocidad_avance_max ? ' - ' . $pass->velocidad_avance_max : '' }}
        </td>
        <td class="b passes-td">{{ $pass->polaridad ?? '' }}</td>
        <td class="b passes-td">{{ strtoupper($pass->progresion ?? '') }}</td>
      </tr>
      @empty
      <tr>
        <td class="b passes-td" colspan="9" style="text-align:center;color:#888;">
          (sin pasadas registradas)
        </td>
      </tr>
      @endforelse
    </tbody>
  </table>
</div>

{{-- RESULTS --}}
<div class="section-title" style="margin-top:3mm;">
  <em>RESULTS / RESULTADOS</em>
</div>

<div style="margin-top:2mm;">

  <div class="result-line">
    {!! $chk($aprobado('VT')) !!}
    <span class="label">Visual Examination of Completed Weld/<strong>Examen visual de la soldadura completa.</strong></span>
    @if($tr('VT')) <span class="bold">{{ strtoupper($tr('VT')) }}</span> @endif
  </div>

  <div class="result-line">
    {!! $chk($aprobado('BT-R') || $aprobado('BT-F')) !!}
    <span class="label">Bend test/<strong>Prueba de plegado:</strong></span>
    @if($tr('BT-R')) <span class="bold">{{ strtoupper($tr('BT-R')) }}</span> @endif
    &nbsp;&nbsp;&nbsp;
    {!! $chk($aprobado('BT-R')) !!}
    <span class="label">Transverse root and face/<strong>Transversal de raíz</strong></span>
    @if($tr('BT-R')) <span class="bold">{{ strtoupper($tr('BT-R')) }}</span> @endif
  </div>

  <div class="result-line">
    {!! $chk($aprobado('NB')) !!}
    <span class="label">Nick Break</span>
    @if($tr('NB')) <span class="bold">{{ strtoupper($tr('NB')) }}</span> @endif
  </div>

</div>

{{-- NOTAS --}}
<div style="margin-top:3mm;">
  <span style="font-size:7.5pt;font-weight:bold;">NOTAS:</span>
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
<div style="margin-top:4mm;" class="aprobado-stamp">
  SOLDADOR APROBADO<br>
  <span style="font-size:9pt;font-weight:normal;">APPROVED WELDER</span>
</div>
@else
<div style="margin-top:4mm;text-align:center;color:#CC0000;">
  <span style="font-size:12pt;font-weight:bold;">SOLDADOR RECHAZADO</span><br>
  <span style="font-size:9pt;">FAILED WELDER</span>
</div>
@endif

{{-- WELDER PHOTO + SIGNATURE --}}
<table style="margin-top:4mm;border:none;">
  <tr>
    {{-- DNI Photo centered --}}
    <td width="40%" style="border:none;text-align:center;vertical-align:middle;">
      @if($fotoDataUri)
        <img src="{{ $fotoDataUri }}" style="max-width:50mm;max-height:62mm;" />
      @else
        <div style="border:0.3mm solid #ccc;width:50mm;height:62mm;text-align:center;font-size:7pt;color:#888;margin:0 auto;">
          <br><br><br>Foto DNI
        </div>
      @endif
    </td>
    {{-- Signature --}}
    <td width="60%" style="border:none;text-align:center;vertical-align:bottom;">
      @if($firmaDataUri)
        <img src="{{ $firmaDataUri }}" style="max-height:20mm;max-width:60mm;" /><br>
      @else
        <div style="height:20mm;"></div>
      @endif
      <div style="font-size:9pt;font-weight:bold;">{{ $inspNombre }}</div>
      <div style="font-size:8pt;">{{ $inspCert }}</div>
    </td>
  </tr>
</table>

{{-- DATE HIGHLIGHT BOTTOM --}}
<div class="date-highlight" style="margin-top:4mm;">
  Date/Fecha: <strong>{{ $fechaCalif }}</strong>
  &nbsp;&nbsp;&nbsp;
  Fecha de Vto. <strong>{{ $fechaVtoDisplay }}</strong>
</div>

{{-- CERTIFICATION TEXT --}}
<div style="margin-top:2mm;" class="cert-text">
  We certify that the statements in this record are correct and that the test coupons were prepared,
  welded, and tested in accordance with the requirements of API STANDARD 1104 Ed 2022<br>
  Nosotros certificamos que las declaraciones en este registro son correctas y que los cupones de la
  prueba fueron preparados, soldados y ensayados de acuerdo a los requisitos de API STANDARD 1104 Ed 2022
</div>
<div class="verify-text" style="margin-top:1mm;">
  VERIFICAR LA AUTENTICIDAD DE ESTE DOCUMENTO ESCANEANDO EL CODIGO QR
</div>
<div class="label right" style="margin-top:0.5mm;">Página 2 de 2</div>

</body>
</html>
