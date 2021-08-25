<div class="cropper text-center" id="posterFullCropper">
    <img src="{{ Module::asset('projects:img/movie-placeholder.jpg') }}" alt="...">
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
<script>
    cropPoster();
</script>