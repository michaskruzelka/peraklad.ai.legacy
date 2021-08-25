<li class="dropdown">
    <a class="navbar-avatar dropdown-toggle" data-toggle="dropdown" href="#" aria-expanded="false"
       data-animation="scale-up" role="button">
          <span class="avatar avatar-online">
            <img src="{{ Auth::user()->getAvatar()->getSrc() }}" alt="аватарка">
            <i></i>
          </span>
    </a>
    <ul class="dropdown-menu" role="menu">
        <li role="presentation">
            <a href="{{ route('workshop::users::profile', ['user' => 'me']) }}" role="menuitem">
                <i class="icon wb-user" aria-hidden="true"></i> Профіль
            </a>
        </li>
        <li class="divider" role="presentation"></li>
        <li role="presentation">
            <a href="{{ route('users::auth::logout') }}" role="menuitem">
                <i class="icon wb-power" aria-hidden="true"></i> Выйсці
            </a>
        </li>
    </ul>
</li>