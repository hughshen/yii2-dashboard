(function () {
    this.initTinyMCE = function () {
        // Defaults params
        var params = {
            uploadUrl: '/media/upload',
            deleteUrl: '/media/delete',
            managerUrl: '/media/list',
            managerTitle: 'File Manager',
            managerPage: '1',
            managerFolder: '',
        };

        if (arguments[1] && typeof arguments[1] === 'object') {
            this.params = bindOptions(params, arguments[1]);
        }

        // Defaults options
        var options = {
            selector: '',
            branding: false,
            valid_children: '+body[style],+a[div|h1|h2|h3|h4|h5|h6|p]',
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
            toolbar: 'undo redo | formatselect | bold italic forecolor backcolor | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | image media filemanager link unlink code',

            relative_urls: false,

            // Upload
            automatic_uploads: true,
            images_reuse_filename: true,
            images_upload_handler: function (blobInfo, success, failure) {
                var formData = new FormData();
                formData.append('files', blobInfo.blob(), blobInfo.filename());

                $.ajax({
                    url: params.uploadUrl,
                    type: 'POST',
                    cache: false,
                    data: formData,
                    dataType: 'json',
                    processData: false,
                    contentType: false,
                    success: function (data) {
                        if (data.status) {
                            success(data.paths[0]);
                        } else {
                            failure(data.message);
                        }
                    },
                    error: function (jqXHR, textStatus) {
                        failure(textStatus);
                    }
                });
            },
            file_picker_types: 'file media',
            file_picker_callback: function (callback, value, meta) {
                var input = document.createElement('input');
                input.setAttribute('type', 'file');

                if (meta.filetype === 'image') {
                    input.setAttribute('accept', 'image/*');
                } else if (meta.filetype === 'media') {
                    input.setAttribute('accept', 'audio/*,video/*');
                } else {
                    input.setAttribute('accept', '*/*');
                }

                input.onchange = function () {
                    var file = this.files[0];
                    var formData = new FormData();
                    formData.append('files', file);

                    $.ajax({
                        url: params.uploadUrl,
                        type: 'POST',
                        cache: false,
                        data: formData,
                        dataType: 'json',
                        processData: false,
                        contentType: false,
                        success: function (data) {
                            if (data.status) {
                                callback(data.paths[0], {title: file.name});
                            } else {
                                window.alert(data.message);
                            }
                        },
                        error: function (jqXHR, textStatus) {
                            window.alert(textStatus);
                        }
                    });
                };

                input.click();
            },

            // Setup
            setup: function (editor) {
                // File manager
                var win;

                function closeHandler() {
                    win.close();
                }

                function insertHandler() {
                    var selected = $(win.getEl()).find('.media-file.selected .media-thumbnail');
                    if (selected.length) {
                        var imgs = '';
                        $(selected).each(function (key, val) {
                            var linkEle = $(val).find('.media-link').eq(0);
                            if (parseInt(linkEle.attr('data-image')) === 1) {
                                imgs += '<img src="' + linkEle.attr('data-url') + '"/>';
                            } else {
                                imgs += '<a href="' + linkEle.attr('data-url') + '">' + linkEle.attr('data-url') + '</a>';
                            }
                        });
                        editor.insertContent(imgs);
                    }
                    win.close();
                }

                function deleteHandler() {
                    var selected = $(win.getEl()).find('.media-file.selected .media-thumbnail');
                    if (selected.length) {
                        editor.windowManager.confirm('Are you sure you want to delete this item?', function (res) {
                            if (res) {
                                var paths = [];
                                $(selected).each(function (key, val) {
                                    paths.push($(val).find('.media-link').eq(0).attr('data-path'));
                                });
                                $.ajax({
                                    url: params.deleteUrl,
                                    type: 'POST',
                                    data: {paths: paths},
                                    dataType: 'json',
                                    success: function (data) {
                                        editor.windowManager.alert(data.message);
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
                    var width = Math.min(window.innerWidth - 40, 860),
                        height = Math.min(window.innerHeight - 120, 500);
                    editor.focus(false);

                    $('body').addClass('modal-open');
                    win = editor.windowManager.open({
                        title: params.managerTitle,
                        body: [{
                            type: 'container',
                            html: '<div class="file-manager-wrap" id="file-manager-wrap" style="height: ' + (height - 40) + 'px;"></div>',
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
                        maximizable: false,
                        onClose: function () {
                            $('body').removeClass('modal-open');
                        }
                    });

                    var winEl = $(win.getEl());
                    winEl.on('click', '.media-file', function () {
                        $(this).toggleClass('selected');
                    });
                    winEl.on('click', '.media-dir .media-thumbnail', function () {
                        params.managerFolder = $(this).find('.media-link').eq(0).attr('data-path');
                        loadFilesHtml();
                    });
                    winEl.on('click', '.breadcrumb .media-link', function () {
                        params.managerFolder = $(this).attr('data-path');
                        loadFilesHtml();
                    });
                    winEl.on('click', '.pagination a', function (e) {
                        e.preventDefault();
                        var page = $(this).attr('data-page');
                        page = parseInt(page);
                        params.managerPage = page + 1;
                        loadFilesHtml();
                    });

                    loadFilesHtml();
                }

                function loadFilesHtml() {
                    $.get(params.managerUrl, {page: params.managerPage, path: params.managerFolder}, function (data) {
                        $(win.getEl()).find('#file-manager-wrap').html(data);
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
