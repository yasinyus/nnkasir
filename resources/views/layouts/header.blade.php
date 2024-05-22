<header class="main-header">
    <!-- Logo -->
    <a href="index2.html" class="logo">
        <!-- mini logo for sidebar mini 50x50 pixels -->
        @php
            $words = explode(' ', $setting->nama_perusahaan);
            $word  = '';
            foreach ($words as $w) {
                $word .= $w[0];
            }
        @endphp
        <span class="logo-mini">{{ $word }}</span>
        <!-- logo for regular state and mobile devices -->
        <span class="logo-lg"><b>{{ $setting->nama_perusahaan }}</b></span>
    </a>
    <!-- Header Navbar: style can be found in header.less -->
    <nav class="navbar navbar-fixed-top">
        <!-- Sidebar toggle button-->
        <a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button">
            <span class="sr-only">Toggle navigation</span>
        </a>
        <div class="collapse navbar-collapse" id="example-1">
			<ul class="nav navbar-nav">
				<li class="{{ (request()->is('transaksi')) ? 'active' : '' }}"><a href="{{ route('transaksi.baru') }}"><i class="fa fa-cart-arrow-down"></i> Transaksi Penjualan</a></li>
				<li class="{{ (request()->is('penjualan')) ? 'active' : '' }}"><a href="{{ route('penjualan.index') }}"><i class="fa fa-book"></i> Laporan Penjualan</a></li>
				{{-- <li class="{{ (request()->is('keluarmasuk')) ? 'active' : '' }}"><a href="{{ route('keluarmasuk.index') }}"><i class="fa fa-car"></i> Mobil Keluar Masuk</a></li> --}}
				<li class="{{ (request()->is('pembelian')) ? 'active' : '' }}"><a href="{{ route('pembelian.index') }}"><i class="fa fa-money"></i> Pembelian Barang</a></li>
				<li class="{{ (request()->is('pengeluaran')) ? 'active' : '' }}"><a href="{{ route('pengeluaran.index') }}"><i class="fa fa-arrow-circle-up"></i> Laporan Pengeluaran</a></li>
				<li class="{{ (request()->is('retur')) ? 'active' : '' }}"><a href="{{ route('retur.index') }}"><i class="fa fa-refresh"></i> Retur Penjualan</a></li>
			</ul>
            <div class="navbar-custom-menu">
                <ul class="nav navbar-nav">
                    <!-- User Account: style can be found in dropdown.less -->
                    <li class="dropdown user user-menu">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                            <img src="{{ url(auth()->user()->foto ?? '') }}" class="user-image img-profil"
                                alt="User Image">
                            <span class="hidden-xs">{{ auth()->user()->name }}</span>
                        </a>
                        <ul class="dropdown-menu">
                            <!-- User image -->
                            <li class="user-header">
                                <img src="{{ url(auth()->user()->foto ?? '') }}" class="img-circle img-profil"
                                    alt="User Image">
    
                                <p>
                                    {{ auth()->user()->name }} - {{ auth()->user()->email }}
                                </p>
                            </li>
                            <!-- Menu Footer-->
                            <li class="user-footer">
                                <div class="pull-left">
                                    <a href="{{ route('user.profil') }}" class="btn btn-default btn-flat">Profil</a>
                                </div>
                                <div class="pull-right">
                                    <a href="#" class="btn btn-default btn-flat"
                                        onclick="$('#logout-form').submit()">Keluar</a>
                                </div>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
		</div>

       
    </nav>
</header>

<form action="{{ route('logout') }}" method="post" id="logout-form" style="display: none;">
    @csrf
</form>