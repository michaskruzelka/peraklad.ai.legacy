<div class="panel panel-bordered">
    <div class="panel-heading">
        <h3 class="panel-title">Звесткі</h3>
        <div class="panel-actions">
            <a class="panel-action icon wb-minus" aria-expanded="true" data-toggle="panel-collapse"
               aria-hidden="true"></a>
            <a class="panel-action icon wb-expand" data-toggle="panel-fullscreen" aria-hidden="true"></a>
            <a class="panel-action icon wb-refresh"
               data-toggle="panel-refresh"
               data-load-type="round-circle"
               data-load-callback="panelRefreshCallback"
               data-url="{{ route('workshop::users::refreshMyProfilePanel', ['user' => $user->getId()]) }}"
               aria-hidden="true"></a>
        </div>
    </div>
    <div class="panel-body">
        @include('users::panels.my-profile.info')
    </div>
</div>
<div class="panel panel-bordered" id="panel-poster">
    <div class="panel-heading">
        <h3 class="panel-title">Загрузіць аватарку</h3>
        <div class="panel-actions">
            <a class="panel-action icon wb-minus" aria-expanded="true" data-toggle="panel-collapse"
               aria-hidden="true"></a>
            <a class="panel-action icon wb-expand" data-toggle="panel-fullscreen" aria-hidden="true"></a>
            <a class="panel-action icon wb-refresh"
               data-toggle="panel-refresh"
               data-load-type="round-circle"
               data-load-callback="panelRefreshCallback"
               data-url="{{ route('workshop::users::refreshAvatarPanel', ['user' => $user->getId()]) }}"
               aria-hidden="true"></a>
        </div>
    </div>
    <div class="panel-body">
        @include('users::panels.my-profile.avatar')
    </div>
</div>
