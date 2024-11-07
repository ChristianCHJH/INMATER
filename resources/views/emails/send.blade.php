<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enviar Correo</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/tagify/4.9.3/tagify.min.css">
    <script src="{{ public_asset('js/utils_fx.js') }}" type="text/javascript"></script>
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js"></script> 
    <script src="https://cdn.tiny.cloud/1/{{ env('TINYMCE_API_KEY') }}/tinymce/5/tinymce.min.js" referrerpolicy="origin"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/tagify/4.9.3/tagify.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        .file-name {
            max-width: 200px; /* Ajusta este valor según el diseño */
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <h1>Enviar Correo</h1>
        <div id="message"></div>
        <form id="emailForm" action="{{ route('send.email') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="form-group">
                <label for="to">Para:</label>
                <input type="text" class="form-control" id="to" name="to" required>
            </div>
            <div class="form-group">
                <label for="cc">CC:</label>
                <input type="text" class="form-control" id="cc" name="cc">
            </div>
            <div class="form-group">
                <label for="bcc">BCC:</label>
                <input type="text" class="form-control" id="bcc" name="bcc">
            </div>
            <div class="form-group">
                <label for="subject">Asunto:</label>
                <input type="text" class="form-control" id="subject" name="subject" required>
            </div>
            <div class="form-group">
                <label for="body">Mensaje:</label>
                <button type="button" class="" data-toggle="modal" data-target="#adjuntarArchivos">
                    <i class="fa-solid fa-paperclip">Adjuntar archivos</i>
                </button> 
                <textarea class="form-control" id="body" name="body" rows="10"></textarea>
            </div>

            <div class="modal fade" id="adjuntarArchivos" tabindex="-1" role="dialog" aria-labelledby="adjuntarArchivosModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="adjuntarArchivosModalLabel">Adjuntar Archivos</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <!-- Adjuntar archivos -->
                            <div class="form-group">
                                <label for="attachments">Archivos Adjuntos:</label>
                                <input type="file" class="form-control-file" id="attachments" name="attachments[]" multiple>
                                <ul id="attachmentList" class="list-group mt-2"></ul>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                            <button type="button" class="btn btn-primary" data-dismiss="modal">Guardar</button>
                        </div>
                    </div>
                </div>
            </div> 
            <button type="submit" class="btn btn-primary">Enviar</button>
        </form>
    </div>
    <script src="{{ asset('js/emails.js') }}"></script> 
</body>
</html>
