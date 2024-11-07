tinymce.init({
    selector: '#body',
    plugins: 'lists link image',
    toolbar: 'bold italic | alignleft aligncenter alignright | bullist numlist | attachFiles',
    setup: function (editor) {
        editor.ui.registry.addButton('attachFiles', {
            text: 'Adjuntar Archivos',
            icon: 'upload',
            onAction: function () {
                //document.getElementById('attachments').click();
            }
        });
    }
});

// Tagify initialization
var inputTo = document.querySelector('#to');
var inputCc = document.querySelector('#cc');
var inputBcc = document.querySelector('#bcc');

new Tagify(inputTo, {
    delimiters: ",", // the key to separate tags
    pattern: /^[\w-\.]+@([\w-]+\.)+[\w-]{2,4}$/, // pattern to validate emails
    keepInvalidTags: true // retain tags that do not match pattern
});

new Tagify(inputCc, {
    delimiters: ",",
    pattern: /^[\w-\.]+@([\w-]+\.)+[\w-]{2,4}$/,
    keepInvalidTags: true
});

new Tagify(inputBcc, {
    delimiters: ",",
    pattern: /^[\w-\.]+@([\w-]+\.)+[\w-]{2,4}$/,
    keepInvalidTags: true
});

// AJAX form submission
$('#emailForm').on('submit', function(event) {  
    event.preventDefault();
    
    // Actualiza el contenido del editor TinyMCE en el campo textarea
    tinymce.triggerSave();

    mostrarLoader('Espere por favor', 'Enviando mensaje...');
    var formData = new FormData(this);
    formData.append('_token', $('input[name=_token]').val());
    form = document.getElementById('emailForm');

    $.ajax({
        url: $('#emailForm').attr('action'),
        method: "POST",
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
            ocultarLoader();
            if(response.status) {
                mostrarToast('info', response.message);
                $('#message').html('<div class="alert alert-success">'+response.message+'</div>');
            } else {
                $('#message').html('<div class="alert alert-danger">'+response.message+'</div>');
                showErrors(form, response.html, true, 4);
                mostrarToast('error', response.message);
            }
            return false;
        },
        error: function(response) {
            ocultarLoader();
            $('#message').html('<div class="alert alert-danger">Error al enviar el correo.</div>');
            return false;
        }
    });
    return false; 
});

$(document).ready(function() {
    let attachmentList = $('#attachmentList');
    let attachments = [];

    // Manejador para adjuntar archivos
    $('#attachments').on('change', function() {
        const newFiles = Array.from(this.files);
        newFiles.forEach((file) => {
            if (!attachments.some(att => att.name === file.name)) {
                attachments.push(file);
                
                let preview = '';
                if (file.type.startsWith('image/')) {
                    preview = `<img src="${URL.createObjectURL(file)}" class="img-thumbnail" width="100" alt="${file.name}">`;
                } else if (file.type === 'application/pdf') {
                    preview = `<object data="${URL.createObjectURL(file)}" type="application/pdf" width="100" height="100">
                                 <embed src="${URL.createObjectURL(file)}" type="application/pdf" />
                                 <p>Este navegador no soporta PDF. Descarga el archivo <a href="${URL.createObjectURL(file)}">aquí</a>.</p>
                               </object>`;
                } else if (file.type.includes('word')) {
                    preview = `<i class="fas fa-file-word fa-2x"></i>`;
                } else if (file.type.includes('excel')) {
                    preview = `<i class="fas fa-file-excel fa-2x"></i>`;
                } else if (file.type.includes('presentation') || file.type.includes('powerpoint')) {
                    preview = `<i class="fas fa-file-powerpoint fa-2x"></i>`;
                } else {
                    preview = `<i class="fas fa-file fa-2x"></i>`;
                }

                const listItem = `<li class="list-group-item d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center">
                        ${preview}
                        <span class="ml-2 file-name">${truncateFileName(file.name)}</span> 
                    </div>
                    <div>
                        <button type="button" class="btn btn-warning btn-sm replace-attachment" data-index="${attachments.length - 1}">Reemplazar</button>
                        <button type="button" class="btn btn-danger btn-sm remove-attachment" data-index="${attachments.length - 1}">Eliminar</button>
                    </div>
                </li>`;
                attachmentList.append(listItem);
                const inputAttachments = document.getElementById('attachments');
                const dataTransfer = new DataTransfer();
                attachments.forEach(file => dataTransfer.items.add(file));
                inputAttachments.files = dataTransfer.files;
            }
        });
    });

    // Manejador para eliminar archivos adjuntos
    attachmentList.on('click', '.remove-attachment', function() {
        const index = $(this).data('index');
        attachments.splice(index, 1);
        $(this).closest('li').remove();
        updateListIndexes();
    });

    // Manejador para reemplazar archivos adjuntos
    attachmentList.on('click', '.replace-attachment', function() {
        const index = $(this).data('index');
        const listItem = $(this).closest('li'); // Capturar referencia al listItem
        const newFileInput = $('<input type="file" class="d-none">').appendTo('body');
        newFileInput.click();
    
        newFileInput.on('change', function() {
            const newFile = this.files[0];
            attachments[index] = newFile; 
            let preview = '';

            if (newFile.type.startsWith('image/')) {
                preview = `<img src="${URL.createObjectURL(newFile)}" class="img-thumbnail" width="100" alt="${newFile.name}">`;
            } else if (newFile.type === 'application/pdf') {
                preview = `<object data="${URL.createObjectURL(newFile)}" type="application/pdf" width="100" height="100">
                             <embed src="${URL.createObjectURL(newFile)}" type="application/pdf" />
                             <p>Este navegador no soporta PDF. Descarga el archivo <a href="${URL.createObjectURL(newFile)}">aquí</a>.</p>
                           </object>`;
            } else if (newFile.type.includes('word')) {
                preview = `<i class="fas fa-file-word fa-2x"></i>`;
            } else if (newFile.type.includes('excel')) {
                preview = `<i class="fas fa-file-excel fa-2x"></i>`;
            } else if (newFile.type.includes('presentation') || newFile.type.includes('powerpoint')) {
                preview = `<i class="fas fa-file-powerpoint fa-2x"></i>`;
            } else {
                preview = `<i class="fas fa-file fa-2x"></i>`;
            }

            // Actualiza el nombre del archivo y la previsualización utilizando la referencia almacenada
            listItem.find('.file-name').text(truncateFileName(newFile.name));
            listItem.find('.d-flex.align-items-center').html(preview);
        });
    });

    // Función para actualizar los índices de los botones
    function updateListIndexes() {
        attachmentList.find('li').each(function(i, li) {
            $(li).find('.remove-attachment').data('index', i);
            $(li).find('.replace-attachment').data('index', i);
        });
    }

    // Función para truncar el nombre del archivo
    function truncateFileName(fileName, maxLength = 20) {
        if (fileName.length <= maxLength) {
            return fileName;
        }
        const truncatedName = fileName.slice(0, maxLength - 3) + '...';
        return truncatedName;
    }
});
