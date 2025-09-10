<nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <ul class="navbar-nav">
        <li class="nav-item">
            <a class="nav-link" data-widget="pushmenu" href="#" role="button">
                <i class="fas fa-bars"></i>
            </a>
        </li>
    </ul>
    <!-- link logout -->
    <ul class="navbar-nav ml-auto">
        <li class="nav-item dropdown">
            <a class="nav-link" data-toggle="dropdown" href="#" aria-expanded="false">
                {{ Auth::user()->nama }}
                <i class="fas fa-caret-down"></i>
            </a>
            <!-- link profile -->
                

            <div class="dropdown-menu dropdown-menu-right">
                <a href="{{ route('profile.show') }}" class="dropdown-item">
                    <i class="fas fa-user mr-2"></i> Profile
                </a>

                <a href="javascript:;" class="dropdown-item" onClick="document.getElementById('form-logout').submit();">
                    <i class="fas fa-sign-out-alt mr-2"></i> Sign Out
                </a>

            </div>
        </li>
    </ul>
    <!-- end of link logout -->
    <ul class="navbar-nav">
        <li class="nav-item">
            <a class="nav-link" data-widget="fullscreen" href="#" role="button">
                <i class="fas fa-expand-arrows-alt"></i>
            </a>
        </li>
    </ul>
</nav>
<form action="/logout" id="form-logout" method="POST" style="display: none">
    @csrf
</form>