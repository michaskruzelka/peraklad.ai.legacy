<style>
    .page-aside-section .step-numbers {
        font-size: 42px;
        line-height: 42px;
        margin-right: 10px;
    }
    .cursor-pointer {
        cursor: pointer;
    }
</style>

<section class="page-aside-section">
    {{--<h5 class="page-aside-title">Тлумачэнні</h5>--}}
    <div class="list-group">
        <div class="list-group-item">
            <div class="step-desc">
                <span class="step-title cursor-pointer" onclick="$(location).attr('href', '{{ route('workshop::projects::my', ['in' => 'owner']) }}');">
                    Кіруе праектамі - {{ $ownsCount }}
                </span>
            </div><br />
            <div class="step-desc">
                <span class="step-title cursor-pointer" onclick="$(location).attr('href', '{{ route('workshop::projects::my', ['in' => 'member']) }}');">
                    Удзельнічае ў праектах - {{ $participatesCount }}
                </span>
            </div><br />
            <div class="step-desc">
                <span class="step-title">Пераклаў субтытраў - {{ $subsCount }}</span>
            </div><br />
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
