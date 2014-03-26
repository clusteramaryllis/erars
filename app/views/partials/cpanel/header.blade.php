<header class="navbar navbar-static-top navbar-inverse">
	<div class="container">
		
		<div class="navbar-header">
			<button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#navbar-collapse-1">
        <span class="sr-only">Toggle navigation</span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </button>
			<a href="#" class="navbar-brand">Aplikasi</a>
		</div><!-- ./navbar-header -->

		<div class="collapse navbar-collapse" id="navbar-collapse-1">
			<ul class="nav navbar-nav">
				<li><a href="#"><i class="fa fa-home"></i> Home</a></li>
				<li class="dropdown {{ URL::getRequest()->is('cpanel/emergency*') ? ' active' : '' }}">
					<a href="{{ action('EmergencyController@getIndex') }}" class="dropdown-toggle" data-toggle="dropdown">
						<i class="fa fa-ambulance"></i> Emergency <b class="caret"></b>
					</a>
					<ul class="dropdown-menu">
						<li class="{{ URL::getRequest()->is('cpanel/emergency/case*') ? ' active' : '' }}"><a href="{{ action('EmergencyController@getIndexEmergency') }}"><i class="fa fa-warning"></i> Kasus</a></li>
						<li class="divider"></li>
						<li class="{{ URL::getRequest()->is('cpanel/emergency/type*') ? ' active' : '' }}"><a href="{{ action('EmergencyController@getIndexType') }}"><i class="fa fa-th-list"></i> Tipe Emergency</a></li>
						<li class="divider"></li>
						<li class="{{ URL::getRequest()->is('cpanel/emergency/statistic*') ? ' active' : '' }}"><a href="{{ action('EmergencyController@getIndexStatistic') }}"><i class="fa fa-bar-chart-o"></i> Statistik</a></li>
					</ul>
				</li>
				<li class="dropdown {{ URL::getRequest()->is('cpanel/facility*') ? ' active' : '' }}"><a href="{{ action('FacilityController@getIndex') }}"><i class="fa fa-building-o"></i> Fasilitas</a></li>
				<li class="dropdown {{ URL::getRequest()->is('cpanel/user*') ? ' active' : '' }}">
					<a href="{{ action('UserController@getIndex') }}" class="dropdown-toggle" data-toggle="dropdown">
						<i class="fa fa-users"></i> User <b class="caret"></b>
					</a>
					<ul class="dropdown-menu">
						<li class="{{ URL::getRequest()->is('cpanel/user/ert*') ? ' active' : '' }}"><a href="{{ action('UserController@getIndexERT') }}"><i class="fa fa-user-md"></i> ERT</a></li>
						<li class="divider"></li>
						<li class="{{ URL::getRequest()->is('cpanel/user/general*') ? ' active' : '' }}"><a href="{{ action('UserController@getIndexUser') }}"><i class="fa fa-user"></i> Umum</a></li>
					</ul>
				</li>
			</ul>

			<ul class="nav navbar-nav navbar-right">
				<li><a href="{{ action('AdminController@requestLogout') }}"><i class="fa fa-sign-out"></i> Logout</a></li>
			</ul>

		</div><!-- ./navbar-collapse -->
	</div>
</header>