{{-- Modal de Previsualización de Reportes en Pantalla (Mobile & Desktop) --}}
<div class="modal fade" id="modalPreviewPdf" tabindex="-1" aria-labelledby="modalPreviewPdfLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-fullscreen-sm-down" style="max-width: 960px;">
        <div class="modal-content shadow-lg border-0">
            <div class="modal-header bg-dark text-white py-2.5 px-3">
                <div class="d-flex align-items-center gap-2 min-w-0">
                    <span class="avatar avatar-xs bg-info-lt text-info rounded-circle flex-shrink-0">
                        <i class="ti ti-file-text"></i>
                    </span>
                    <div class="min-w-0">
                        <h5 class="modal-title fw-bold mb-0 text-white text-truncate" id="modalPreviewPdfLabel" style="font-size: 0.92rem;">
                            Vista Previa de Reporte
                        </h5>
                        <div class="text-white-50" style="font-size: 0.72rem;">SGP Chicamán &middot; Centro de Atención Permanente</div>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-2 flex-shrink-0 ms-2">
                    <a href="#" id="btnDownloadPreviewPdf" class="btn btn-sm btn-success px-2 py-1" download title="Descargar archivo PDF">
                        <i class="ti ti-download me-1"></i><span class="d-none d-sm-inline">Descargar</span>
                    </a>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
            </div>
            <div class="modal-body p-0 position-relative" style="background: #334155; min-height: 460px; height: 75vh;">
                {{-- Spinner mientras carga el PDF --}}
                <div id="previewPdfLoading" class="position-absolute top-50 start-50 translate-middle text-center text-white" style="z-index: 10;">
                    <div class="spinner-border text-info mb-2" role="status" style="width: 2.2rem; height: 2.2rem;"></div>
                    <div class="fw-semibold small">Generando vista previa del reporte...</div>
                    <div class="text-white-50" style="font-size: 0.75rem;">Por favor espere un momento</div>
                </div>

                {{-- Visor iFrame del PDF --}}
                <iframe id="previewPdfFrame" 
                        src="about:blank" 
                        style="width: 100%; height: 100%; border: none; display: none;"
                        onload="if(this.src && this.src !== 'about:blank'){ const s = document.getElementById('previewPdfLoading'); if(s) s.style.display='none'; this.style.display='block'; }">
                </iframe>
            </div>
            <div class="modal-footer py-2 px-3 bg-light justify-content-between">
                <div class="text-secondary small d-none d-sm-block" style="font-size: 0.75rem;">
                    <i class="ti ti-info-circle me-1"></i> Previsualización en pantalla. Puede descargar el archivo o imprimirlo directamente.
                </div>
                <div class="d-flex gap-2 ms-auto w-100 w-sm-auto justify-content-end">
                    <a href="#" id="btnOpenNewTabPreviewPdf" target="_blank" class="btn btn-sm btn-outline-secondary flex-fill flex-sm-grow-0">
                        <i class="ti ti-external-link me-1"></i> Abrir en pestaña
                    </a>
                    <button type="button" class="btn btn-sm btn-secondary flex-fill flex-sm-grow-0" data-bs-dismiss="modal">
                        Cerrar
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const modalEl = document.getElementById('modalPreviewPdf');
    if (!modalEl) return;

    let bsModal = null;
    const frame = document.getElementById('previewPdfFrame');
    const spinner = document.getElementById('previewPdfLoading');
    const titleEl = document.getElementById('modalPreviewPdfLabel');
    const downloadBtn = document.getElementById('btnDownloadPreviewPdf');
    const openNewTabBtn = document.getElementById('btnOpenNewTabPreviewPdf');

    // Delegación de eventos para capturar clicks en cualquier .btn-preview-pdf
    document.addEventListener('click', function(e) {
        const btn = e.target.closest('.btn-preview-pdf');
        if (!btn) return;

        e.preventDefault();
        const previewUrl = btn.getAttribute('data-preview-url') || btn.getAttribute('href');
        const downloadUrl = btn.getAttribute('data-download-url') || previewUrl.replace('preview=1', '');
        const title = btn.getAttribute('data-title') || 'Vista Previa del Reporte';

        if (titleEl) titleEl.textContent = title;
        if (downloadBtn) downloadBtn.href = downloadUrl;
        if (openNewTabBtn) openNewTabBtn.href = previewUrl;

        // Mostrar indicador de carga
        if (spinner) spinner.style.display = 'block';
        if (frame) {
            frame.style.display = 'none';
            frame.src = previewUrl;
        }

        if (!bsModal) {
            bsModal = new bootstrap.Modal(modalEl);
        }
        bsModal.show();
    });

    // Liberar memoria del iframe al cerrar el modal
    modalEl.addEventListener('hidden.bs.modal', function() {
        if (frame) {
            frame.src = 'about:blank';
            frame.style.display = 'none';
        }
        if (spinner) spinner.style.display = 'block';
    });
});
</script>
