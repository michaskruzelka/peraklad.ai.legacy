<div>
    <div>
        <ul class="site-menu">
            @foreach($categories as $category)
                <li class="site-menu-category">{{ $category }}</li>
                @foreach($items as $item)
                    @if($item->getCategory() == $category)
                        <li class="
                            site-menu-item
                            @if($item->hasChildren()) has-sub @endif
                            @if($item->isActive()) active open @endif
                        ">
                            @if($item->hasChildren())
                                <a href="javascript:void(0)">
                                    <i class="site-menu-icon {{ $item->getProperty('icon-class') }}" aria-hidden="true"></i>
                                    <span class="site-menu-title">{{ $item->getTitle() }}</span>
                                    <span class="site-menu-arrow"></span>
                                </a>
                                <ul class="site-menu-sub">
                                    @foreach($item->getChildren() as $child)
                                        <li class="site-menu-item
                                        @if($child->hasChildren()) has-sub @endif
                                        @if($child->isActive()) active open @endif
                                        ">
                                            @if($child->hasChildren())
                                                <a href="javascript:void(0)">
                                                    <i class="site-menu-icon {{ $child->getProperty('icon-class') }}" aria-hidden="true"></i>
                                                    <span class="site-menu-title">{{ $child->getTitle() }}</span>
                                                    <span class="site-menu-arrow"></span>
                                                </a>
                                                <ul class="site-menu-sub">
                                                    @foreach($child->getChildren() as $childChild)
                                                        <li class="site-menu-item">
                                                            <a class="animsition-link" href="{{ $childChild->getUrl() }}">
                                                                <span class="site-menu-title">{{ $childChild->getTitle() }}</span>
                                                            </a>
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            @else
                                                <a class="animsition-link" href="{{ $child->getUrl() }}">
                                                    <span class="site-menu-title">{{ $child->getTitle() }}</span>
                                                </a>
                                            @endif
                                        </li>
                                    @endforeach
                                </ul>
                            @else
                                <a class="animsition-link" href="{{ $item->getUrl() }}">
                                    <i class="site-menu-icon {{ $item->getProperty('icon-class') }}" aria-hidden="true"></i>
                                    <span class="site-menu-title">{{ $item->getTitle() }}</span>
                                </a>
                            @endif
                        </li>
                    @endif
                @endforeach
            @endforeach
        </ul>
    </div>
</div>