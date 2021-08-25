var approveMember;
var initProjectsList = function() {
    $.Selective.prototype._options.unselect = function(self, opt) {
        self._trigger("beforeOptionUnselect", opt);
        opt.prop('selected', false);
        self._trigger("afterOptionUnselect", opt);
        return self;
    };
    $.Selective.prototype.itemAdd = function(data, content) {
        this._trigger("beforeItemAdd", data);
        this._items.add(this, data, content);
        this._trigger("afterItemAdd", data);
        return this;
    };

    window.AppProjects = {
        handleSlidePanel: function() {
            if (typeof $.slidePanel === 'undefined') return;

            var defaults = $.components.getDefaults("slidePanel");
            var options = $.extend({}, defaults, {
                template: function(options) {
                    return '<div class="' + options.classes.base + ' ' + options.classes.base + '-' + options.direction + '">' +
                        '<div class="' + options.classes.base + '-scrollable"><div>' +
                        '<div class="' + options.classes.content + '"></div>' +
                        '</div></div>' +
                        '<div class="' + options.classes.base + '-handler"></div>' +
                        '</div>';
                },
                afterLoad: function() {
                    this.$panel.find('.' + this.options.classes.base + '-scrollable').asScrollable({
                        namespace: 'scrollable',
                        contentSelector: '>',
                        containerSelector: '>'
                    });
                }
            });

            $(document).on('click', '#my-project-list [data-toggle=slidePanel]', function(e) {
                $(this).closest('.panel').data('panel-api').leaveFullscreen();
                $.slidePanel.show({
                    url: $(this).data('url'),
                    settings: {
                        cache: false
                    }
                }, options);

                e.stopPropagation();
            });
        },
        handlePagination: function() {
            var $allPage = $('#my-project-list .pagination li');
            $allPage.on('click', function(e) {
                e.preventDefault();
                if ($(this).hasClass('disabled')) {
                    return false;
                }
                var refreshEl = $(this).closest('.panel').find('a[data-toggle="panel-refresh"]');
                $(refreshEl).attr('data-url', $(this).children('a').attr('href'));
                $(refreshEl).trigger('click');
            });
        },
        handleSelective: function() {
            var self = this;
            $('[data-plugin="jquery-selective"]').each(function() {
                var items = $(this).attr('data-selective-members');
                var member = $(this).attr('data-selectable-members');
                member = JSON.parse(member);
                items = JSON.parse(items);
                var elem = $(this);
                $(this).selective({
                    namespace: 'addMember',
                    local: member,
                    selected: items,
                    buildFromHtml: false,
                    tpl: {
                        optionValue: function(data) {
                            return data.id;
                        },
                        frame: function() {
                            return '<div class="' + this.namespace + '">' +
                                this.options.tpl.items.call(this) +
                                '<div class="' + this.namespace + '-trigger">' +
                                this.options.tpl.triggerButton.call(this) +
                                '<div class="' + this.namespace + '-trigger-dropdown">' +
                                this.options.tpl.list.call(this) +
                                '</div>' +
                                '</div>' +
                                '</div>'

                            i++;
                        },
                        triggerButton: function() {
                            return '<div class="' + this.namespace + '-trigger-button"><i class="wb-plus"></i></div>';
                        },
                        listItem: function(data) {
                            return '<li class="' + this.namespace + '-list-item"><img class="avatar" src="' + data.avatar + '">' + data.id + '</li>';
                        },
                        item: function(data) {
                            var item = '<li class="' + this.namespace + '-item"';
                            item += '><img class="avatar';
                            if (data.state == 'pe') {
                                item += ' gray';
                            }
                            item += '" src="' + data.avatar + '" title="' + data.id + '" onclick="$(location).attr(\'href\', \'' + data.url + '\');">';
                            if (data.removable) {
                                item += this.options.tpl.itemRemove.call(this);
                            }
                            if (data.acceptable && data.state == 'pe') {
                                item += '<span onclick="approveMember(this, \'' + data.id +'\')" class="' + this.namespace + '-accept"><i class="wb-check-circle"></i></span>';
                            }
                            item += '</li>';
                            return item;
                        },
                        itemRemove: function() {
                            return '<span class="' + this.namespace + '-remove"><i class="wb-minus-circle"></i></span>';
                        },
                        option: function(data) {
                            return '<option value="' + this.options.tpl.optionValue.call(this, data) + '">' + data.id + '</option>';
                        }
                    },
                    onAfterOptionUnselect: function(opt) {
                        var data = {
                            'id': $(opt).val()
                        };
                        var url = $(opt).closest('tr').attr('data-remove-member-url');
                        sendRequest(url, data);
                    },
                    onAfterItemAdd: function(opt) {
                        var data = {
                            'id': opt.id
                        };
                        var url = $(elem).closest('tr').attr('data-add-member-url');
                        sendRequest(url, data);
                    }
                });
            });
        }
    };
    approveMember = function(elem, id) {
        var data = {
            'id': id
        };
        var url = $(elem).closest('tr').attr('data-approve-member-url');
        sendRequest(url, data);
        var avatar = $(elem).closest('li').find('.avatar');
        $(avatar).removeClass('gray');
        $(elem).remove();
    };

    var sendRequest = function(url, data) {
        $.ajax({
            url: url,
            data: data,
            type: 'POST',
            success: function(result) {
                if (result.status && result.status == 'ok') {
                    toastr.success(result.response);
                } else {
                    toastr.error('Нешта не так...');
                }
            },
            error: function(result) {
                toastr.error(result.status + ' - ' + result.statusText, 'Нешта не так...');
                if (result.status == 422) {
                    $.each(result.responseJSON, function(key, arr) {
                        $.each(arr, function(index, message) {
                            toastr.warning(message);
                        });
                    });
                }
            }
        });
    };
};

var initSubtitleVersions = function() {
    var errorText = 'Нешта не так...';

    $('.likeVersion').on('click', function() {
        var elem = this;
        var data = {
            id: $(elem).attr('data-id'),
            checked:  ! $(elem).hasClass('active')
        };
        $.ajax({
            url: $(elem).attr('data-url'),
            type: 'POST',
            data: data,
            success: function(result) {
                if ( ! result.status || result.status != 'ok') {
                    toastr.warning(result.response, errorText);
                    return false;
                }
                var tooltipTitle;
                if (data.checked) {
                    tooltipTitle = 'Адмяніць';
                } else {
                    tooltipTitle = 'Упадабаць';
                }
                $(elem).attr('data-original-title', tooltipTitle);
            },
            error: function(result) {
                if (result.status == 422) {
                    $.each(result.responseJSON, function(key, arr) {
                        $.each(arr, function(index, message) {
                            toastr.warning(message);
                        });
                    });
                } else {
                    toastr.error(result.status + ' - ' + result.statusText, errorText);
                }
            }
        });
    });

    $('.approveVersion').on('click', function() {
        var elem = this;
        var data = {
            id: $(elem).attr('data-id'),
            checked:  ! $(elem).hasClass('active')
        };
        $.ajax({
            url: $(elem).attr('data-url'),
            type: 'POST',
            data: data,
            success: function(result) {
                if ( ! result.status || result.status != 'ok') {
                    toastr.warning(result.response, errorText);
                    return false;
                }
                toastr.success(result.response);
                //var refreshEl = $(elem).closest('.panel').find('[data-toggle="panel-refresh"]').get(0);
                //$(refreshEl).trigger('click');
                var tooltipTitle;
                if (data.checked) {
                    $('#translationSubtitle').find('[data-toggle="panel-refresh"]').trigger('click');
                    tooltipTitle = 'Адхіліць';
                } else {
                    tooltipTitle = 'Выбраць';
                }
                $(elem).attr('data-original-title', tooltipTitle);
            },
            error: function(result) {
                if (result.status == 422) {
                    $.each(result.responseJSON, function(key, arr) {
                        $.each(arr, function(index, message) {
                            toastr.warning(message);
                        });
                    });
                } else {
                    toastr.error(result.status + ' - ' + result.statusText, errorText);
                }
            }
        });
    });

    $('.removeVersion').on('click', function() {
        var elem = this;
        var data = {
            id: $(elem).attr('data-id')
        };
        $.ajax({
            url: $(elem).attr('data-url'),
            type: 'POST',
            data: data,
            success: function(result) {
                if ( ! result.status || result.status != 'ok') {
                    toastr.warning(result.response, errorText);
                    return false;
                }
                toastr.success(result.response);
                var refreshEl = $(elem).closest('.panel').find('[data-toggle="panel-refresh"]').get(0);
                $(refreshEl).trigger('click');
            },
            error: function(result) {
                if (result.status == 422) {
                    $.each(result.responseJSON, function(key, arr) {
                        $.each(arr, function(index, message) {
                            toastr.warning(message);
                        });
                    });
                } else {
                    toastr.error(result.status + ' - ' + result.statusText, errorText);
                }
            }
        });
    });

    $('#add-subtitle-version').formValidation({
        framework: "bootstrap",
        excluded: [':disabled'],
        button: {
            disabled: 'disabled'
        },
        icon: {
            valid: 'icon wb-check',
            invalid: 'icon wb-warning',
            validating: 'icon wb-warning'
        },
        fields: {
            text: {
                validators: {
                    notEmpty: {
                        message: 'Увядзіце пераклад'
                    },
                    stringLength: {
                        message: 'Скараціце пераклад да 500 сімвалаў',
                        max: 500
                    }
                }
            }
        }
    }).on('success.form.fv', function(e) {
        e.preventDefault();
        var $form = $(e.target);
        var fv = $form.data('formValidation');
        fv.disableSubmitButtons(true);
        $.ajax({
            url: $form.attr('action'),
            type: 'POST',
            data: $form.serialize(),
            success: function(result) {
                if ( ! result.status || result.status != 'ok') {
                    toastr.warning(result.response, errorText);
                    return false;
                }
                toastr.success(result.response);
                var refreshEl = $form.closest('.panel').find('[data-toggle="panel-refresh"]').get(0);
                $(refreshEl).trigger('click');
            },
            error: function(result) {
                if (result.status == 422) {
                    $.each(result.responseJSON, function(key, arr) {
                        $.each(arr, function(index, message) {
                            toastr.warning(message);
                        });
                    });
                } else {
                    toastr.error(result.status + ' - ' + result.statusText, errorText);
                }
            },
            complete: function(result) {
                fv.resetForm();
                fv.disableSubmitButtons(false);
            }
        });
    });
};

var initSubtitleComments = function() {
    $('.replyComment').on('click', function () {
        var userId = $(this).attr('data-user');
        var replyTo = $('#addCommentReplyTo').val();
        replyTo = replyTo.split(',');
        replyTo = replyTo.filter(function (v) {
            return v !== ''
        });
        if ($.inArray(userId, replyTo) == -1) {
            replyTo.push(userId);
            $('#addCommentReplyTo').val(replyTo);
        }
        if ($('#addCommentText').val().toLowerCase().indexOf(userId) == -1) {
            $('#addCommentText').val(userId + ', ' + $('#addCommentText').val());
        }
        $('#addCommentText').focus();
    });

    $('.removeComment').on('click', function () {
        var elem = this;
        var data = {
            id: $(elem).attr('data-id')
        };
        var errorText = 'Нешта не так...';
        $.ajax({
            url: $(elem).attr('data-url'),
            type: 'POST',
            data: data,
            success: function (result) {
                if (!result.status || result.status != 'ok') {
                    toastr.warning(result.response, errorText);
                    return false;
                }
                toastr.success(result.response);
                var refreshEl = $(elem).closest('.panel').find('[data-toggle="panel-refresh"]').get(0);
                $(refreshEl).trigger('click');
            },
            error: function (result) {
                if (result.status == 422) {
                    $.each(result.responseJSON, function (key, arr) {
                        $.each(arr, function (index, message) {
                            toastr.warning(message);
                        });
                    });
                } else {
                    toastr.error(result.status + ' - ' + result.statusText, errorText);
                }
            }
        });
    });

    $('#add-subtitle-comment').formValidation({
        framework: "bootstrap",
        excluded: [':disabled'],
        button: {
            disabled: 'disabled'
        },
        icon: {
            valid: 'icon wb-check',
            invalid: 'icon wb-warning',
            validating: 'icon wb-warning'
        },
        fields: {
            text: {
                validators: {
                    notEmpty: {
                        message: 'Увядзіце каментарый',
                    },
                    stringLength: {
                        message: 'Скараціце каментар да 500 сімвалаў',
                        max: 500
                    }
                }
            }
        }
    }).on('success.form.fv', function (e) {
        e.preventDefault();
        var $form = $(e.target);
        var fv = $form.data('formValidation');
        fv.disableSubmitButtons(true);
        var errorText = 'Нешта не так...';
        $.ajax({
            url: $form.attr('action'),
            type: 'POST',
            data: $form.serialize(),
            success: function (result) {
                if (!result.status || result.status != 'ok') {
                    toastr.warning(result.response, errorText);
                    return false;
                }
                toastr.success(result.response);
                var refreshEl = $form.closest('.panel').find('[data-toggle="panel-refresh"]').get(0);
                $(refreshEl).trigger('click');
            },
            error: function (result) {
                if (result.status == 422) {
                    $.each(result.responseJSON, function (key, arr) {
                        $.each(arr, function (index, message) {
                            toastr.warning(message);
                        });
                    });
                } else {
                    toastr.error(result.status + ' - ' + result.statusText, errorText);
                }
            },
            complete: function (result) {
                fv.resetForm();
                fv.disableSubmitButtons(false);
            }
        });
    });
};

var initSubtitleTranslation = function(underSaveUrl, initSummernote) {
    var translatedText;
    var summernoteOptions = {
        height: 90,
        toolbar: [
            ['style', ['bold', 'italic', 'underline']],
            ['view', ['codeview']]
        ],
        lang: 'be-BE',
        disableDragAndDrop: true,
        focus: true,
        callbacks: {
            onFocus: function(event) {
                translatedText = event.target.innerHTML;
            },
            onBlur: function(event) {
                var content = event.target.innerHTML;
                if (content == translatedText) {
                    return false;
                }
                var data = {
                    content: content
                };
                $.ajax({
                    url: underSaveUrl,
                    data: data,
                    type: 'POST',
                    success: function(result) {
                    if (result.status && result.status == 'ok') {

                    } else {
                        toastr.error('Нешта не так...');
                    }
                },
                error: function(result) {
                    if (result.status == 422) {
                        $.each(result.responseJSON, function(key, arr) {
                            $.each(arr, function(index, message) {
                                toastr.warning(message);
                            });
                        });
                    } else {
                        toastr.error(result.status + ' - ' + result.statusText, 'Нешта не так...');
                    }
                }
            });
            }
        }
    };
    $('#edit-translation').on('click', function() {
        $('#translatedEditor').summernote(summernoteOptions);
        $(this).addClass('hidden');
        $('#save-translation').removeClass('hidden');
        var content = $('#translatedEditor').summernote('code');
        var data = {
            content: content
        };
        var elem = this;
        $.ajax({
            url: $(elem).attr('data-url'),
            data: data,
            type: 'POST',
            success: function(result) {
                if (result.status && result.status == 'ok') {
//                    toastr.success(result.response);
                } else {
                    toastr.error('Нешта не так...');
                }
            },
            error: function(result) {
                if (result.status == 422) {
                    $.each(result.responseJSON, function(key, arr) {
                        $.each(arr, function(index, message) {
                            toastr.warning(message);
                        });
                    });
                } else {
                    toastr.error(result.status + ' - ' + result.statusText, 'Нешта не так...');
                }
            }
        });
    });

    $('#save-translation').on('click', function() {
        var content = $('#translatedEditor').summernote('code');
        $('#translatedEditor').summernote('destroy');
        $(this).addClass('hidden');
        $('#edit-translation').removeClass('hidden');
        var data = {
            content: content
        };
        var elem = this;
        $.ajax({
            url: $(elem).attr('data-url'),
            data: data,
            type: 'POST',
            success: function(result) {
                if (result.status && result.status == 'ok') {
                    toastr.success(result.response);
                } else {
                    toastr.error('Нешта не так...');
                }
            },
            error: function(result) {
                if (result.status == 422) {
                    $.each(result.responseJSON, function(key, arr) {
                        $.each(arr, function(index, message) {
                            toastr.warning(message);
                        });
                    });
                } else {
                    toastr.error(result.status + ' - ' + result.statusText, 'Нешта не так...');
                }
            }
        });
    });

    if (initSummernote) {
        $(document).ready(function() {
            $('#translatedEditor').summernote(summernoteOptions);
        });
    }
};

var initTimeRangeSlider = function(updateTimingUrl, isEditable, startMin, startMax, rangeMin, rangeMax) {
    var getTimeDisplayVal = function(duration) {
        var milliseconds = parseInt((duration%1000))
            , seconds = parseInt((duration/1000)%60)
            , minutes = parseInt((duration/(1000*60))%60)
            , hours = parseInt((duration/(1000*60*60))%24);

        var milliseconds = ("00"+milliseconds).slice(-3);

        hours = (hours < 10) ? "0" + hours : hours;
        minutes = (minutes < 10) ? "0" + minutes : minutes;
        seconds = (seconds < 10) ? "0" + seconds : seconds;

        return hours + ":" + minutes + ":" + seconds + "," + milliseconds;
    };

    var limitSlider = document.getElementById('slider-limit');

    noUiSlider.create(limitSlider, {
        start: [
            startMin,
            startMax
        ],
        tooltips: [ true, true ],
        behaviour: 'drag',
        connect: true,
        range: {
            'min': rangeMin,
            'max': rangeMax
        }
    });

    if ( ! isEditable) {
        limitSlider.setAttribute('disabled', true);
    }

    limitSlider.noUiSlider.on('update', function( values, handle ) {
        var tooltip = $('.noUi-tooltip').get(handle);
        $(tooltip).text(getTimeDisplayVal(values[handle]));
    });

    limitSlider.noUiSlider.on('change', function( values ) {
        var data = {
            bottomLine: getTimeDisplayVal(values[0]),
            topLine: getTimeDisplayVal(values[1])
        };
        $.ajax({
            url: updateTimingUrl,
            data: data,
            type: 'POST',
            success: function(result) {
                if (result.status && result.status == 'ok') {
                    toastr.success(result.response);
                } else {
                    toastr.error('Нешта не так...');
                }
            },
            error: function(result) {
                if (result.status == 422) {
                    $.each(result.responseJSON, function(key, arr) {
                        $.each(arr, function(index, message) {
                            toastr.warning(message);
                        });
                    });
                } else {
                    toastr.error(result.status + ' - ' + result.statusText, 'Нешта не так...');
                }
            }
        });
    });
};

var cropPoster = function() {
    var $posterFullCropper = $("#posterFullCropper img");
    var options = {
        aspectRatio: 1 / 1.47,
        responsive: true,
        minCropBoxWidth: 91
    };
    // set up cropper
    $posterFullCropper.cropper(options);
    // set up method buttons
    $(document).on("click", "[data-cropper-method]", function() {
        var data = $(this).data(),
            method = $(this).data('cropper-method'),
            result;
        if (method) {
            result = $posterFullCropper.cropper(method, data.option);
        }
        if (method === 'getCroppedCanvas') {
            if ($(result).is('canvas')) {
                $('#poster-image').not(':has(img)').append('<img/>');
                $('#poster-image').find('img').attr("src", result.toDataURL());
                $('#poster-hidden').val(result.toDataURL());
                toastr.success('Калі ласка, захавайце змены.', 'Плакат паспяхова абрэзаны!');
            }
        }
    });

    var panel = $posterFullCropper.parents('.panel').get(0);
    $(panel).on('enter.fullscreen.uikit.panel leave.fullscreen.uikit.panel shown.uikit.panel', function() {
        $("#posterFullCropper img").data('cropper').build();
    });
    $(panel).on('loading.done.uikit.panel', function() {
        $(this).find('[data-toggle="tooltip"]').tooltip();
    });

    // deal wtih uploading
    var $inputImage = $("#inputImage");
    if (window.FileReader) {
        $inputImage.change(function() {
            var fileReader = new FileReader(),
                files = this.files,
                file;
            if (!files.length) {
                return;
            }
            file = files[0];
            if (/^image\/\w+$/.test(file.type)) {
                fileReader.readAsDataURL(file);
                fileReader.onload = function() {
                    $posterFullCropper.cropper("reset", true).cropper("replace", this.result);
                    $inputImage.val("");
                };
            } else {
                toastr.warning("Калі ласка, выберыце файл.");
            }
        });
    } else {
        $inputImage.addClass("hide");
    }
};

var releaseFormInit = function(
    updateUrl,
    restoreUrl,
    destroyUrl
) {
    $('#releases-table').footable();
    $(":checkbox[name='private_mode']").labelauty();
    $('.update-mode').labelauty();
    var select2Config = {
        width: "style",
        language: "be",
        minimumResultsForSearch: "Infinity"
    };
    $('select[name="orthography"]').select2(select2Config);
    $('.update-orthography').select2(select2Config);
    $('select[name="release_episode"]').select2(select2Config);
    $('.release-episode').select2(select2Config);
    $('#pick-release').on('click', function() {
        var selector = $('#opensubtitlesModal').find('.search-result').get(0);
        var ripName = $(selector).children("option:selected").text();
        var releaseData = $(selector).val().split('.');
        var releaseId = releaseData[0];
        var charset = releaseData[1];
        var modal = $('#opensubtitlesModal');
        modal.modal('hide');
        $('#new_rip_name').val(ripName);
        $('#opensubtitles-id').val(releaseId);
        $('#opensubtitles-charset').val(charset);
        setTimeout(function() {
            toastr.info('Цяпер вы можаце загрузіць субтытры', 'Рэліз выбраны');
        }, 500);
    });

    $('#upload-release').on('click', function() {
        var form = $('#release-store');
        var ripName = $.trim($('#new_rip_name').val());
        if (ripName.length < 1) {
            toastr.warning('Імя рыпа павінна быць запоўнена', 'Увага!');
            return false;
        } else if ($('#opensubtitles-id').val() == '') {
            var file = $('input[name="new_release_file"]').get(0).files[0];
            if ( ! file) {
                toastr.warning('Выберыце файл альбо скарыстайцеся пошукам', 'Увага!');
                return false;
            } else if (file.type != 'application/x-subrip') {
                toastr.warning('Выберыце файл фармата srt', 'Няверны фармат файла');
                return false;
            } else if (file.length > 10000000) {
                toastr.warning('Занадта вялікі файл', 'Увага!');
                return false;
            }
        }
        var l = Ladda.create(this);
        l.start();
        var formData = new FormData();
        var params = form.serializeArray();
        var projectId = $('input[name="id"]').val();
        formData.append('new_release_file', file);
        formData.append('projectId', projectId);
        $.each(params, function(i, val) {
            formData.append(val.name, val.value);
        });
        if ($('#isTranslated').hasClass('active')) {
            formData.append('isTranslated', true);
        }
        $.ajax({
            url: form.attr('action'),
            data: formData,
            cache: false,
            contentType: false,
            processData: false,
            type: 'POST',
            success: function(result) {
                if (result.status && result.status == 'ok') {
                    toastr.success(result.response);
                    var refreshEl = $(form).closest('.panel').find('[data-toggle="panel-refresh"]').get(0);
                    $(refreshEl).trigger('click');
                } else {
                    toastr.warning(result.response, 'Увага!');
                }
            },
            error: function(result) {
                l.stop();
                toastr.error(result.status + ' - ' + result.statusText, 'Нешта не так...');
                if (result.status == 422) {
                    $.each(result.responseJSON, function(key, arr) {
                        $.each(arr, function(index, message) {
                            toastr.warning(message);
                        });
                    });
                }
            },
            complete: function(result) {
                l.stop();
            }
        });
    });

    $('.update-orthography').on('change', function() {
        var data = {
            id: $(this).closest('tr').find('[name="release_id"]').val(),
            orthography: $(this).val()
        };
        updateRelease(data);
    });

    $('.update-mode').on('change', function() {
        var data = {
            id: $(this).closest('tr').find('[name="release_id"]').val(),
            mode: $(this).is(':checked')
        };
        updateRelease(data);
    });

    $('.update-rip').on('change', function() {
        var ripName = $.trim($(this).val());
        if (ripName.length < 1) {
            toastr.warning('Імя рыпа павінна быць запоўнена', 'Увага!');
            return false;
        }
        var data = {
            id: $(this).closest('tr').find('[name="release_id"]').val(),
            rip_name: ripName
        };
        updateRelease(data);
    });

    $('.release-episode').on('change', function() {
        var data = {
            id: $(this).closest('tr').find('[name="release_id"]').val(),
            episode_id: $(this).val()
        };
        updateRelease(data);
    });

    var updateRelease = function(data) {
        $.ajax({
            url: updateUrl,
            data: data,
            type: 'POST',
            success: function(result) {
                if (result.status && result.status == 'ok') {
                    toastr.success(result.response);

                } else {
                    toastr.error('Нешта не так...');
                }
            },
            error: function(result) {
                toastr.error(result.status + ' - ' + result.statusText, 'Нешта не так...');
                if (result.status == 422) {
                    $.each(result.responseJSON, function(key, arr) {
                        $.each(arr, function(index, message) {
                            toastr.warning(message);
                        });
                    });
                }
            }
        });
    };

    var panel = $('#release-store').parents('.panel').get(0);
    $(panel).on('loading.done.uikit.panel', function() {
        $('#isTranslated').tooltip();
        $(this).find('[data-toggle="tooltip"]').tooltip();
    });

    $('.restore-release').on('click', function() {
        var elem = this;
        var data = {
            id: $(elem).closest('tr').find('[name="release_id"]').val()
        };
        $.ajax({
            url: restoreUrl,
            data: data,
            type: 'POST',
            success: function(result) {
                if (result.status == 'ok') {
                    toastr.success(result.response);
                    setTimeout(function() {
                        var refreshEl = $(elem).closest('.panel').find('[data-toggle="panel-refresh"]').get(0);
                        $(refreshEl).trigger('click');
                    }, 500);
                } else {
                    toastr.error(result.response);
                }
            },
            error: function(result) {
                toastr.error(result.status + ' - ' + result.statusText, 'Нешта не так...');
                if (result.status == 422) {
                    $.each(result.responseJSON, function(key, arr) {
                        $.each(arr, function(index, message) {
                            toastr.warning(message);
                        });
                    });
                }
            }
        });
    });

    $('.delete-release').on('click', function() {
        var elem = this;
        var modal = $('#removeModal');
        modal.modal('toggle');
        $(modal).find('#removeConfirmed').on('click', function() {
            modal.modal('toggle');
            var data = {
                id: $(elem).closest('tr').find('[name="release_id"]').val()
            };
            $.ajax({
                url: destroyUrl,
                data: data,
                type: 'POST',
                success: function(result) {
                    if (result.status && result.status == 'ok') {
                        toastr.success(result.response);
                        toastr.info('Цягам наступных 10 хвілін Вы можаце аднавіць рэліз');
                        setTimeout(function() {
                            var refreshEl = $(elem).closest('.panel').find('[data-toggle="panel-refresh"]').get(0);
                            $(refreshEl).trigger('click');
                        }, 500);
                    } else {
                        toastr.error('Нешта не так...');
                    }
                },
                error: function(result) {
                    toastr.error(result.status + ' - ' + result.statusText, 'Нешта не так...');
                    if (result.status == 422) {
                        $.each(result.responseJSON, function(key, arr) {
                            $.each(arr, function(index, message) {
                                toastr.warning(message);
                            });
                        });
                    }
                }
            });
        });
    });
    $.components.register("input-group-file", {
        api: function() {
            $(document).on("change", ".input-group-file [type=file]", function() {
                var $this = $(this);
                var $text = $(this).parents('.input-group-file').find('.form-control');
                var value = "";

                $.each($this[0].files, function(i, file) {
                    value += file.name + ", ";
                });
                value = value.substring(0, value.length - 2);
                value = value.substr(0, value.lastIndexOf('.'));

                $text.val(value);
                $('#opensubtitles-id').val(null);
                $('#opensubtitles-charset').val(null);
            });
        }
    });
};

var searchOpensubtitles = function(elem, url) {
    var l = Ladda.create(elem);
    l.start();
    var warningTitle = 'Нічога не знойдзена.';
    var warningText = 'Нажаль, спатрэбіцца самастойны пошук.';
    var errorText = 'Нешта не так...';
    var projectId = $('input[name="id"]').val();
    var episodeId = $(elem).closest('tr').find('select[name="release_episode"]').val();
    var data = {
        projectId: projectId,
        episodeId: episodeId
    };
    if ( ! projectId &&  ! episodeId) {
        toastr.warning(warningText, warningTitle);
        l.stop();
        return false;
    }
    $.ajax({
        url: url,
        data: data,
        type: 'POST',
        success: function(result) {
            if (result.status && result.status == 'ok') {
                var searchData = [];
                $.each(result.response, function(i, release) {
                    searchData[i] = {};
                    searchData[i].id = release.IDSubtitleFile + '.' + release.SubEncoding;
                    searchData[i].text = release.MovieReleaseName ? release.MovieReleaseName : release.MovieName;
                    searchData[i].subText = release.MovieName;
                    searchData[i].rating = release.SubRating;
                });
                var modal = $('#opensubtitlesModal');
                var modalSelector = modal.find('.search-result').get(0);
                if (typeof $(modalSelector).data('select2') !== 'undefined') {
                    $(modalSelector).select2('destroy');
                    $(modalSelector).empty();
                }
                $(modalSelector).select2({
                    width: "style",
                    language: "be",
                    data: searchData,
                    minimumResultsForSearch: "Infinity",
                    escapeMarkup: function (markup) { return markup; },
                    templateResult: function(release) {
                        if ( ! release.id) { return release.text; }
                        var markup = "<div class='select-result-release'>"
                                + "<div class='title'>" + release.text + "</div>"
                                + "<div class='sub-text'>Фільм: " + release.subText + ". Рэйтынг субтытраў: "
                                + release.rating + "</div>"
                                + "</div>"
                            ;
                        return markup;
                    }
                });
                modal.modal('toggle');
            } else {
                toastr.warning(warningText, warningTitle);
            }
        },
        error: function(result) {
            toastr.error(result.status + ' - ' + result.statusText, errorText);
        },
        complete: function(result) {
            l.stop();
        }
    });
};

var projectKeySearch = function(url) {
    var errorText = 'Нешта не так...';
    $("#projects-list-selector").select2({
        width: "style",
        language: "be",
        ajax: {
            url: url,
            type: 'POST',
            data: function (params) {
                return {
                    key: params.term,
                    page: params.page
                };
            },
            processResults: function (data, params) {
                params.page = params.page || 1;
                results = [];
                $.each(data.response.items, function (i, v) {
                    var o = {};
                    o.id = v.url;
                    o.translatedTitle = v.translatedTitle;
                    o.originalTitle = v.originalTitle;
                    o.plot = v.plot;
                    o.year = v.year;
                    o.poster = v.poster;
                    results.push(o);
                });
                return {
                    results: results,
                    pagination: {
                        more: (params.page * 10) < data.response.totalCount
                    }
                };
            },
            error: function(result) {
                if (result.status != 0) {
                    toastr.error(result.status + ' - ' + result.statusText, errorText);
                }
            },
            delay: 250,
            cache: true
        },
        placeholder: '--Выберыце фільм--',
        escapeMarkup: function (markup) { return markup; },
        minimumInputLength: 3,
        maximumInputLength: 100,
        templateResult: function (proj) {
            if (proj.loading) return proj.text;
            var markup = "<div class='select2-result-movie'>" +
                    "<div class='poster'><img src='" + proj.poster + "' /></div>" +
                    "<div class='title'>" + proj.translatedTitle + " | " +
                    proj.originalTitle
                ;
            if (proj.year) {
                markup += " (" + proj.year + ")";
            }
            markup += "</div><div class='author'></div>";
            if (proj.plot) {
                markup += "<div class='plot'>" + proj.plot + "</div>";
            }
            return markup;
        }
    }).on('change', function(e) {
        e.preventDefault();
        window.location.href = $(e.currentTarget).val();
    });
};

var movieEpisodes = [];

var projectFormInit = function(
    searchImdbUrl,
    placeholderSrc
) {
    var select2Config = {
        width: "style",
        language: "be"
    };
    $('#lang').select2(select2Config);
    $('#movie-type').select2({width:"style",language:"be",minimumResultsForSearch:"Infinity"});


    $('#movie-series').dependsOn({'#movie-type' : {
        values: ['series']
    }},{
        onDisable: function() {
            $('#series-table').find('input').prop('disabled', true);
        },
        onEnable: function() {
            $('#series-table').find('input').prop('disabled', false);
        }
    });
    $('#year').dependsOn({'#movie-type' : {
        values: ['movie']
    }},{
        hide : false,
        onDisable: function() {
            var fv = $('#project-store').data('formValidation');
            fv.enableFieldValidators('year', false);
            fv.resetForm();
        },
        onEnable: function() {
            var fv = $('#project-store').data('formValidation');
            fv.enableFieldValidators('year', true);
            fv.resetForm();
        }
    });
    $('#form-group-year').dependsOn({'#movie-type' : {
        values: ['movie']
    }});

    $('.imdb').click(function(e){
        e.preventDefault();
        var l = Ladda.create(this);
        l.start();
        var warningText = 'Паспрабуйце карэктна запоўніць поле';
        var warningTitle = 'Нічога не знойдзена.';
        var errorText = 'Нешта не так...';
        var successText = 'Знойдзена!';
        var url = searchImdbUrl;
        var data = {};
        var dataSearchKey = $(this).attr('data-search-key');
        var dataSearchValue = $('#' + dataSearchKey).val();
        if ( ! dataSearchValue) {
            toastr.warning(warningText, warningTitle);
            l.stop();
            return false;
        }
        data[dataSearchKey] = dataSearchValue;
        $.ajax({
            url: url,
            data: data,
            type: 'POST',
            success: function(result) {
                if (result.status && result.status == 'ok') {
                    if (result.response.episodes !== null) {
                        movieEpisodes = result.response.episodes;
                        addEpisodesRows();
                    } else {
                        movieEpisodes = [];
                    }
                    $('#imdb-id').val(result.response.id);
                    $('#title').val(result.response.title);
                    $('#score').val(result.response.rating);
                    $('#plot').val(result.response.plot);
                    $('#year').val(result.response.year);
                    if (result.response.language !== null) {
                        $('#lang').val(result.response.language);
                        $('#lang').trigger('change');
                    }
                    if (result.response.type !== null && $('#movie-type').is(':enabled')) {
                        $('#movie-type').val(result.response.type);
                        $('#movie-type').trigger('change');
                    }
                    $('#poster-image').not(':has(img)').append('<img/>');
                    if (result.response.poster !== null) {
                        var imgSrc = result.response.poster;
                    } else {
                        var imgSrc = placeholderSrc;
                    }
                    $('#poster-image').find('img').attr("src", imgSrc);
                    $('#poster-hidden').val(imgSrc);
                    var fv = $('#project-store').data('formValidation');
                    fv.resetForm();
                    fv.validate();
                    toastr.success(successText);
                } else {
                    toastr.warning(warningText, warningTitle);
                }
            },
            error: function(result) {
                toastr.error(result.status + ' - ' + result.statusText, errorText);
            },
            complete: function(result) {
                l.stop();
            }
        });
    });
};

var projectDestroyInit = function(destroyUrl) {
    $('#delete-project').on('click', function () {
        var elem = this;
        var modal = $('#removeProjectModal');
        modal.modal('toggle');
        $(modal).find('#removeProjectConfirmed').on('click', function() {
            var errorText = 'Нешта не так';
            data = {
                id: $(elem).closest('form').find('input[name="id"]').val()
            };
            $.ajax({
                url: destroyUrl,
                data: data,
                type: 'POST',
                success: function(result) {
                    if (result.status && result.status == 'ok') {
                        $(location).attr("href", result.response);
                    } else {
                        toastr.warning(result.response);
                    }
                },
                error: function(result) {
                    toastr.error(result.status + ' - ' + result.statusText, errorText);
                    if (result.status == 422) {
                        $.each(result.responseJSON, function(key, arr) {
                            $.each(arr, function(index, message) {
                                toastr.error(message);
                            });
                        });
                    }
                }
            });
        });
    });
};

var projectStoreFormInit = function() {
    $('#project-store').formValidation({
        framework: "bootstrap",
        excluded: [':disabled'],
        button: {
            selector: '#save-project',
            disabled: 'disabled'
        },
        icon: {
            valid: 'icon wb-check',
            invalid: 'icon wb-warning',
            validating: 'icon wb-warning'
        },
        fields: {
            translated_title: {
                validators: {
                    notEmpty: {
                        message: 'Увядзіце назву па-беларуску',
                    },
                    stringLength: {
                        message: 'Скараціце назву да 100 сімвалаў',
                        max: 100
                    }
                }
            },
            original_title: {
                validators: {
                    notEmpty: {
                        message: 'Увядзіце арыгінальную назву'
                    },
                    stringLength: {
                        message: 'Скараціце назву да 100 сімвалаў',
                        max: 100
                    }
                }
            },
            year: {
                validators: {
                    notEmpty: {
                        message: 'Увядзіце год'
                    },
                    between: {
                        min: 1900,
                        max: new Date().getFullYear(),
                        message: 'Увядзіце карэктны год'
                    }
                }
            },
            score: {
                validators: {
                    regexp: {
                        regexp: /^((10){1}|(\d){1}[\.,]{1}(\d){1})?$/,
                        message: 'Увядзіце карэктны рэйтынг'
                    }
                }
            },
            plot: {
                validators: {
                    stringLength: {
                        message: 'Скараціце сюжэт да 500 сімвалаў',
                        max: 500
                    }
                }
            },
            'season[]': {
                validators: {
                    notEmpty: {},
                    between: {
                        min: 0,
                        max: 99
                    }
                },
                icon: false
            },
            'episode[]': {
                validators: {
                    notEmpty: {},
                    between: {
                        min: 0,
                        max: 99
                    }
                },
                icon: false
            },
            'episode_original_title[]': {
                validators: {
                    notEmpty: {},
                    stringLength: {max: 100}
                },
                icon: false
            },
            'episode_translated_title[]': {
                validators: {
                    notEmpty: {},
                    stringLength: {max: 100}
                },
                icon: false
            },
            'episode_year[]': {
                validators: {
                    notEmpty: {},
                    between: {
                        min: 1900,
                        max: new Date().getFullYear()
                    }
                },
                icon: false
            }
        }
    }).on('success.form.fv', function(e) {
        e.preventDefault();
    }).on('err.field.fv', function(e, data) {
        var exclFields = [
            'season[]',
            'episode[]',
            'episode_original_title[]',
            'episode_translated_title[]',
            'episode_year[]'
        ];
        if ($.inArray(data.field, exclFields) !== -1) {
            data.element
                .data('fv.messages')
                .find('.help-block[data-fv-for="' + data.field + '"]').hide()
            ;
        }
    });

    $('#project-store').submit(function(e) {
        var $form = $(e.target);
        var fv = $form.data('formValidation');
        if ( ! fv.isValid()) {
            return false;
        }
        fv.disableSubmitButtons(true);
        var errorText = 'Нешта не так...';
        $.ajax({
            url: $form.attr('action'),
            type: 'POST',
            data: $form.serialize(),
            success: function(result) {
                if ( ! result.status || result.status != 'ok') {
                    toastr.warning(result.response, errorText);
                    return false;
                }
                if (result.response.url) {
                    $(location).attr("href", result.response.url);
                } else if (result.response.message) {
                    toastr.success(result.response.message);
                    var refreshEl = $form.closest('.panel').find('[data-toggle="panel-refresh"]').get(0);
                    $(refreshEl).trigger('click');
                }
            },
            error: function(result) {
                toastr.error(result.status + ' - ' + result.statusText, errorText);
                if (result.status == 422) {
                    $.each(result.responseJSON, function(key, arr) {
                        $.each(arr, function(index, message) {
                            toastr.warning(message);
                        });
                    });
                }
            },
            complete: function(result) {
                fv.resetForm();
                //fv.disableSubmitButtons(false);
            }
        });
    });
};

var seriesTableInit = function() {
    var seriesTable = $('#series-table');
    seriesTable.footable().on('click', '.delete-row-btn', function() {
        var row = $(this).parents('tr:first');
        removeRow(row);
    });
    $('#add-ser').click(function() {
        var footable = seriesTable.data('footable');
        var newRow = getEpisodeRow();
        footable.appendRow(newRow);
        addValidationField();
    });
};

var getEpisodeRow = function(season, episode, originalTitle, translatedTitle, year, imdbId, id, open) {
    season = typeof season !== 'undefined' ? season : '';
    episode = typeof episode !== 'undefined' ? episode : '';
    originalTitle = typeof originalTitle !== 'undefined' ? originalTitle : '';
    translatedTitle = typeof translatedTitle !== 'undefined' ? translatedTitle : '';
    year = typeof year !== 'undefined' ? year : '';
    imdbId = typeof imdbId !== 'undefined' ? imdbId : '';
    id = typeof id !== 'undefined' ? id : '';
    open = typeof open !== 'undefined' ? open : true;
    var newRow = '<tr><td class="season form-group"><input class="form-control" maxlength="2" name="season[]" type="text" value="' + season + '"';
    if ( ! open) {
        newRow += ' disabled';
    }
    newRow += '></td><td class="episode form-group"><input class="form-control" maxlength="2" name="episode[]" type="text" value="' + episode + '"';
    if ( ! open) {
        newRow += ' disabled';
    }
    newRow += '></td><td class="form-group"><input class="form-control" maxlength="100" name="episode_original_title[]" type="text" value="' + originalTitle + '"';
    if ( ! open) {
        newRow += ' disabled';
    }
    newRow += '></td><td class="form-group"><input class="form-control" maxlength="100" name="episode_translated_title[]" type="text" value="' + translatedTitle + '"';
    if ( ! open) {
        newRow += ' disabled';
    }
    newRow += '></td><td class="episode_year form-group"><input class="form-control" maxlength="4" name="episode_year[]" type="text" value="' + year + '"';
    if ( ! open) {
        newRow += ' disabled';
    }
    newRow += '></td><td class="episode_del">';
    if (open) {
        newRow += '<button type="button" class="btn btn-sm btn-icon btn-pure btn-default delete-row-btn" data-toggle="tooltip" data-original-title="Выдаліць">' +
            '<i class="icon wb-close" aria-hidden="true"></i>' +
            '</button>'
        ;
        newRow += '<input type="hidden" name="episode_imdb_id[]" value="' + imdbId + '" />' +
            '<input type="hidden" name="episode_id[]" value="' + id + '" />'
        ;
    }

    newRow += '</td></tr>';
    return newRow;
};
var clearRow = function(row) {
    $(row).find('[name="season[]"]').val('');
    $(row).find('[name="episode[]"]').val('');
    $(row).find('[name="episode_original_title[]"]').val('');
    $(row).find('[name="episode_translated_title[]"]').val('');
    $(row).find('[name="episode_year[]"]').val('');
    $(row).find('[name="episode_id[]"]').val('');
    $(row).find('[name="episode_imdb_id[]"]').val('');
};
var removeRow = function(row) {
    if ($('#series-table').find('tbody tr').length == 1) {
        clearRow(row);
        return false;
    }
    var footable = $('#series-table').data('footable');
    var fields = [
        $(row).find('[name="season[]"]').get(0),
        $(row).find('[name="episode[]"]').get(0),
        $(row).find('[name="episode_original_title[]"]').get(0),
        $(row).find('[name="episode_translated_title[]"]').get(0),
        $(row).find('[name="episode_year[]"]').get(0)
    ];
    var fv = $('#project-store').data('formValidation');
    $.each(fields, function(index, field) {
        fv.removeField($(field));
    });
    footable.removeRow(row);
};
var addValidationField = function() {
    var fields = [
        $('#series-table').find('[name="season[]"]').last(),
        $('#series-table').find('[name="episode[]"]').last(),
        $('#series-table').find('[name="episode_original_title[]"]').last(),
        $('#series-table').find('[name="episode_translated_title[]"]').last(),
        $('#series-table').find('[name="episode_year[]"]').last()
    ];
    var fv = $('#project-store').data('formValidation');
    $.each(fields, function(index, field) {
        fv.addField($(field));
    });
};
var addEpisodesRows = function() {
    var footable = $('#series-table').data('footable');
    if (movieEpisodes.length > 0) {
        $.each($('tr', $('#series-table').find('tbody')), function(index, row) {
            removeRow(row);
        });
        $.each(movieEpisodes, function(index, value) {
            var newRow = getEpisodeRow(
                value.season,
                value.episode,
                value.originalTitle,
                '',
                value.year,
                value.imdbId,
                undefined,
                true
            );
            footable.appendRow(newRow);
            addValidationField();
        });
    }
};