<section class="page-aside-section">
    <h5 class="page-aside-title">Пошук</h5>
    <div class="list-group">
        <div class="list-group-item">
            <div class="form-group">
                <select class="form-control" id="users-list-selector">
                    <optgroup>
                        <option value="{{ route('workshop::users::view') }}" @if ('all' == $userId) selected @endif>усе</option>
                        <option value="{{ route('workshop::users::view', ['userId' => Auth::id()]) }}" @if (Auth::id() == $userId) selected @endif>{{ Auth::id() }}</option>
                    </optgroup>
                    <optgroup>
                        @foreach ($users as $id)
                            <option value="{{ route('workshop::users::view', [
                                'userId' => $id
                            ]) }}"
                                @if ($id == $userId) selected @endif
                            >{{ $id }}</option>
                        @endforeach
                    </optgroup>
                </select>
            </div>
        </div>
    </div>
</section>
<section class="page-aside-section">
    <h5 class="page-aside-title">Тлумачэнні</h5>
    <div class="list-group">
        <span class="list-group-item">

        </span>
    </div>
</section>
<script>

    var select2Config = {
        width: "style",
        language: "be"
    };
    $('#users-list-selector').select2(select2Config).on('change', function(e) {
        e.preventDefault();
        $(location).attr("href", $(e.currentTarget).val());
    });
</script>