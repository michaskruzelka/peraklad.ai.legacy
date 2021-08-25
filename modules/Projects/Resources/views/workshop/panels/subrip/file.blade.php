@if ($file)

@if ($release->belongsToYou() || $release->includesYou() || $release->isPublic())

@unless($release->isCompleted())
    <div class="well red-600">
        КАЛІ ЛАСКА, МАЙЦЕ НА ЎВАЗЕ, ШТО ПЕРАКЛАД СУБТЫТРАЎ ЯШЧЭ НЕ ЗАВЕРШАНЫ
    </div>
@endunless

<pre class="ace-editor" id="subRipData">{!! htmlentities($file->decode()) !!}</pre>
@if ($release->belongsToYou())
<div class="text-right">
    {!! Form::button('Захаваць', [
        'type' => 'button',
        'disabled' => 'disabled',
        'class' => 'btn btn-primary',
        'id' => 'save-subrip',
        'data-url' => route('workshop::releases::subrip::save', ['release' => $release->getId(), 'format' => $abc])
    ]) !!}
</div>
@endif

<script>
    var subRipEditorReadOnly;
    @if ($release->belongsToYou())
        subRipEditorReadOnly = false;
    @else
        subRipEditorReadOnly = true;
    @endif
</script>

@push('bottom-scripts')
<script>
    (function(document, window, $) {
        'use strict';

        var initSubRipEditor = function(readOnly) {
            var editor = ace.edit('subRipData');
            editor.container.style.opacity = "";
            editor.setOption("maxLines", 80);
            editor.setOption("showPrintMargin", true);
            editor.setOption("printMarginColumn", 35);
            editor.setAutoScrollEditorIntoView(true);

            if (readOnly) {
                editor.setOption('readOnly', true);
            } else {
                var saveButton = document.getElementById("save-subrip");
                editor.on("input", function() {
                    saveButton.disabled = editor.session.getUndoManager().isClean();
                });
                saveButton.addEventListener("click", function() {
                    editor.session.getUndoManager().markClean();
                    saveButton.disabled = editor.session.getUndoManager().isClean();
                    var data = {
                        text: editor.getValue()
                    };
                    var errorText = 'Нешта не так...';
                    $.ajax({
                        url: $(saveButton).attr('data-url'),
                        type: 'POST',
                        data: data,
                        success: function(result) {
                            if ( ! result.status || result.status != 'ok') {
                                toastr.warning(result.response, errorText);
                                return false;
                            }
                            toastr.success(result.response);
                            var refreshEl = $('#subRipFileContent').find('[data-toggle="panel-refresh"]').get(0);
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
            }
        };

        initSubRipEditor(subRipEditorReadOnly);
        var panel = $('#subRipFileContent');
        $(panel).on('loading.done.uikit.panel', function () {
            initSubRipEditor(subRipEditorReadOnly);
        });

    })(document, window, jQuery);



</script>
@endpush

@else

<div class="well red-600">
    НАЖАЛЬ, ПРАГЛЯД ФАЙЛА ЗАБАРОНЕНЫ ЎЛАДАЛЬНІКАМ
</div>

@endif

@else

<div class="well red-600">
    ФАЙЛ ПУСТЫ
</div>

@endif