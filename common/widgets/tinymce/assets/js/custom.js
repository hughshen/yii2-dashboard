(function() {
    this.initTinyMCE = function() {
        // Defaults params
        var params = {
            uploadUrl: 'index.php?r=media/json-upload',
            deleteUrl: 'index.php?r=media/json-delete',
            managerUrl: 'index.php?r=media/manager-list',
            managerPage: '1',
            managerFolder: '',
            allowExtensions: ['jpg', 'png', 'gif', 'jpeg'],
            allowMimeTypes: 'image/jpeg,image/png,image/gif,image/jpeg',
        };

        if (arguments[1] && typeof arguments[1] === 'object') {
            this.params = bindOptions(params, arguments[1]);
        }

        // Defaults options
        var options = {
            selector: '',
            branding: false,
            valid_children: '+body[style]',
            valid_elements: '*[*]',
            height: 300,
            // language: 'zh_TW',
            theme: 'modern',
            mobile: {
                theme: 'mobile',
                plugins: 'undo redo bold italic underline link image bullist numlist fontsizeselect forecolor styleselect removeformat',
                toolbar: 'undo redo bold italic underline link image bullist numlist fontsizeselect forecolor styleselect removeformat',
            },
            plugins: [
                'advlist autolink lists link image charmap print preview anchor textcolor',
                'searchreplace visualblocks code',
                'insertdatetime media table contextmenu paste code imagetools',
            ],
            toolbar: 'undo redo | formatselect | bold italic forecolor backcolor | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | localimage filemanager link unlink code',

            relative_urls: false,

            // Upload
            automatic_uploads: true,
            images_reuse_filename: true,
            file_picker_types: 'image',
            images_upload_handler: function(blobInfo, success, failure) {
                $.ajax({
                    url: params.uploadUrl,
                    type: 'POST',
                    data: {
                        image: 'data:image/jpeg;base64,' + blobInfo.base64(),
                        filename: blobInfo.name(),
                    },
                    success: function (data) {
                        if (data.status) {
                            success(data.path);
                        } else {
                            alert(data.msg);
                        }
                    }
                });
            },
            // Setup
            setup: function(editor) {
                // Insert local image
                var node = document.createElement('input');
                node.setAttribute('type', 'file');
                node.setAttribute('multiple', true);
                node.setAttribute('accept', params.allowMimeTypes);

                var Exts = params.allowExtensions;

                function toggleLocalImageInput() {
                    node.click();
                }

                function uploadHandler(file) {
                    var ext = file.name.split('.').pop().toLowerCase(),
                    extError = Exts.indexOf(ext) == -1;
                    if (extError) {
                        editor.windowManager.alert('Only files with these extensions are allowed: ' + Exts.join(', '));
                        return;
                    }

                    if (typeof window.FileReader !== 'function') {
                        editor.windowManager.alert('Your browser does not support HTML5 native. Try Firefox 3, Safari 4, or Chrome.');
                    }

                    var reader = new FileReader();
                    reader.readAsDataURL(file);
                    reader.onload = function() {
                        var id = 'blobid' + (new Date()).getTime() + Math.floor(Math.random() * 1000);
                        var blobCache =  editor.editorUpload.blobCache;
                        var base64 = reader.result.split(',')[1];
                        var blobInfo = blobCache.create(id, file, base64);
                        blobCache.add(blobInfo);
                        if (typeof editor.settings.images_upload_handler === 'function') {
                            editor.settings.images_upload_handler(blobInfo, function(path) {
                                editor.insertContent('<img src="' + path + '"/>');
                            }, function(path) {});
                        }
                    };
                }

                node.onchange = function() {
                    if (this.files.length) {
                        for (var i = 0; i < this.files.length; i++) {
                            uploadHandler(this.files[i]);
                        }
                    }
                    node.value = '';
                };

                editor.addButton('localimage', {
                    icon: 'image',
                    tooltip: 'Insert file',
                    onclick: toggleLocalImageInput,
                });
                // Insert local image end

                // File manager
                var win;

                function closeHandler() {
                    win.close();
                }
                function insertHandler() {
                    var selected = $(win.getEl()).find('.file-thumb.selected');
                    if (selected.length) {
                        var imgs = '';
                        $(selected).each(function(key, val) {
                            imgs += '<img src="' + $(val).attr('data-url') + '"/>';
                        });
                        editor.insertContent(imgs);
                    }
                    win.close();
                }
                function deleteHandler() {
                    var selected = $(win.getEl()).find('.file-thumb.selected');
                    if (selected.length) {
                        editor.windowManager.confirm('Are you sure you want to delete this item?', function(res) {
                            if (res) {
                                var paths = [];
                                $(selected).each(function(key, val) {
                                    paths.push($(val).attr('data-url'));
                                });
                                $.ajax({
                                    url: params.deleteUrl,
                                    type: 'POST',
                                    data: {paths: paths},
                                    dataType: 'json',
                                    success: function(data) {
                                        console.log(data);
                                        editor.windowManager.alert(data.msg);
                                        if (data.status) loadFilesHtml();
                                    }
                                });
                            }
                        });
                    } else {
                        editor.windowManager.alert('Please select files');
                    }
                }
                function winOpenHander() {
                    var width = Math.min(window.innerWidth - 40, 800),
                        height = Math.min(window.innerHeight - 120, 500);
                    editor.focus(false);

                    win = editor.windowManager.open({
                        title: 'File List',
                        body: [{
                            type: 'container',
                            html: '<div class="filemanager-wrap" style="height: ' + (height - 40) + 'px;"></div>',
                        }],
                        buttons: [
                            {text: 'Confirm', subtype: 'primary', onclick: insertHandler},
                            {text: 'Delete', onclick: deleteHandler},
                            {text: 'Cancel', onclick: closeHandler},
                        ],
                        width: width,
                        height: height,
                        inline: true,
                        resizable: false,
                        maximizable: false
                    });

                    var winEl = $(win.getEl());
                    winEl.on('click', '.file-thumb', function() {
                        $(this).toggleClass('selected');
                    });
                    winEl.on('click', '.folder-icon', function() {
                        params.managerFolder = $(this).attr('data-folder');
                        loadFilesHtml();
                    });
                    winEl.on('click', '.pagination a', function(e) {
                        e.preventDefault();
                        var page = $(this).attr('data-page');
                        page = parseInt(page);
                        params.managerPage = page + 1;
                        loadFilesHtml();
                    });

                    loadFilesHtml();
                }
                function loadFilesHtml() {
                    $.get(params.managerUrl, {page: params.managerPage, folder: params.managerFolder}, function(data) {
                        $(win.getEl()).find('.filemanager-wrap').html(data);
                    });
                }

                editor.addButton('filemanager', {
                    icon: 'browse',
                    tooltip: 'Select file',
                    onclick: winOpenHander,
                });
                // File manager end
            }
        };

        if (arguments[0] && typeof arguments[0] === 'object') {
            this.options = bindOptions(options, arguments[0]);
        }

        if (options.selector.length) {
            tinymce.init(options);
        }
    };

    function bindOptions(options, properties) {
        var property;
        for (property in properties) {
            if (properties.hasOwnProperty(property)) {
                options[property] = properties[property];
            }
        }
        return options;
    }
}());

