<div class="cropper text-center" id="avatarFullCropper">
    <img src="{{ $user->getAvatar()->getSrc() }}"/>
</div>
<div class="cropper-toolbar text-center">
    <div class="btn-group margin-bottom-20">
        <button type="button" class="btn btn-primary" data-cropper-method="zoom" data-option="0.1"
                data-toggle="tooltip" data-container="body" title="Павялічыць">
                    <span class="cropper-tooltip" title="павялічыць">
                      <i class="wb-zoom-in"></i>
                    </span>
        </button>
        <button type="button" class="btn btn-primary" data-cropper-method="zoom" data-option="-0.1"
                data-toggle="tooltip" data-container="body" title="Зменшыць">
                    <span class="cropper-tooltip" title="зменшыць">
                      <i class="wb-zoom-out"></i>
                    </span>
        </button>
        <button type="button" class="btn btn-primary" data-cropper-method="rotate" data-option="-90"
                data-toggle="tooltip" data-container="body" title="Павярнуць">
                    <span class="cropper-tooltip" title="налева 90°">
                      <i class="wb-arrow-left cropper-flip-horizontal"></i>
                    </span>
        </button>
        <button type="button" class="btn btn-primary" data-cropper-method="rotate" data-option="90"
                data-toggle="tooltip" data-container="body" title="Павярнуць">
                    <span class="cropper-tooltip" title="направа 90°">
                      <i class="wb-arrow-right"></i>
                    </span>
        </button>
        <button type="button" class="btn btn-primary" data-cropper-method="rotate" data-option="-5"
                data-toggle="tooltip" data-container="body" title="Трошкі налева">
                    <span class="cropper-tooltip" title="налева 5°">
                      <i class="wb-refresh cropper-flip-horizontal"></i>
                    </span>
        </button>
        <button type="button" class="btn btn-primary" data-cropper-method="rotate" data-option="5"
                data-toggle="tooltip" data-container="body" title="Трошкі направа">
                    <span class="cropper-tooltip" title="направа 5°">
                      <i class="icon wb-reload" aria-hidden="true"></i>
                    </span>
        </button>
    </div>
    <div class="btn-group margin-bottom-20">
        <button type="button" class="btn btn-primary" data-cropper-method="setDragMode"
                data-option="move" data-toggle="tooltip" data-container="body"
                title="Зрушыць">
                    <span class="cropper-tooltip" title="зрушыць">
                      <i class="icon wb-move" aria-hidden="true"></i>
                    </span>
        </button>
        <button type="button" class="btn btn-primary" data-cropper-method="setDragMode"
                data-option="crop" data-toggle="tooltip" data-container="body"
                title="Памер">
                    <span class="cropper-tooltip" title="памер">
                      <i class="icon wb-crop" aria-hidden="true"></i>
                    </span>
        </button>
        <button type="button" class="btn btn-primary" data-cropper-method="getCroppedCanvas"
                data-option='{ "width": 182, "height": 268 }' data-toggle="tooltip"
                data-container="body" title="Абрэзаць">
                    <span class="cropper-tooltip" title="Абрэзаць">
                      <i class="icon wb-image" aria-hidden="true"></i>
                    </span>
        </button>
        <label class="btn btn-primary" data-toggle="tooltip" for="inputImage" data-container="body"
               title="Загрузіць">
            <input type="file" class="hide" id="inputImage" name="file" accept="image/*">
                    <span class="cropper-tooltip" title="Загрузіць выяву">
                      <i class="icon wb-upload" aria-hidden="true"></i>
                    </span>
        </label>
    </div>
</div>
<style>
    #avatarFullCropper {
        max-height: 128px;
    }
</style>
<script>
    var $posterFullCropper = $("#avatarFullCropper img");
    var options = {
        aspectRatio: 1 / 1,
        responsive: true,
        minCropBoxWidth: 128
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
                $('#user-avatar').not(':has(img)').append('<img/>');
                $('#user-avatar').find('img').attr("src", result.toDataURL());
                $('#avatar-hidden').val(result.toDataURL());
                toastr.success('Калі ласка, захавайце змены.', 'Аватарка паспяхова абрэзаная!');
            }
        }
    });

    var panel = $posterFullCropper.parents('.panel').get(0);
    $(panel).on('enter.fullscreen.uikit.panel leave.fullscreen.uikit.panel shown.uikit.panel', function() {
        $("#avatarFullCropper img").data('cropper').build();
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
    //cropPoster();
</script>