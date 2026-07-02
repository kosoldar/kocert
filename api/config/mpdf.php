<?php

return [

    /*
     * Configuración base para todos los PDFs de certificados RCS/WPQ.
     * Se puede sobreescribir por instancia en PdfCertificadoService.
     */

    'mode'          => 'utf-8',
    'format'        => 'A4',
    'orientation'   => 'P',            // Portrait
    'margin_left'   => 10,
    'margin_right'  => 10,
    'margin_top'    => 10,
    'margin_bottom' => 10,
    'margin_header' => 0,
    'margin_footer' => 0,

    /*
     * Directorio temporal que mPDF usa para cache de fuentes e imágenes.
     * Debe tener permisos de escritura.
     */
    'temp_dir'      => storage_path('app/mpdf_tmp'),

    /*
     * Directorio donde se guardan los PDFs generados de certificados.
     */
    'output_dir'    => storage_path('app/pdfs'),

];
