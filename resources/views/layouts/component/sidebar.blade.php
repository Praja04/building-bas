 <div class="sidenav-menu">

     <!-- Brand Logo -->
     <a href="/" class="logo">
         <span class="logo logo-light">
             <span class="logo-lg"><img src="{{ asset('assets') }}/images/logos/bas.png" alt="logo"
                     style="height: 50px"></span>
             <span class="logo-sm"><img src="{{ asset('assets') }}/images/logos/bas.png" alt="small logo"></span>
         </span>

         <span class="logo logo-dark">
             <span class="logo-lg"><img src="{{ asset('assets') }}/images/logos/bas.png" alt="dark logo"
                     style="height: 50px"></span>
             <span class="logo-sm"><img src="{{ asset('assets') }}/images/logos/bas.png" alt="small logo"></span>
         </span>
     </a>

     <!-- Sidebar Hover Menu Toggle Button -->
     <button class="button-on-hover">
         <i class="ti ti-menu-4 fs-22 align-middle"></i>
     </button>

     <!-- Full Sidebar Menu Close Button -->
     <button class="button-close-offcanvas">
         <i class="ti ti-x align-middle"></i>
     </button>

     <div class="scrollbar" data-simplebar>
         <!--- Menu -->
         <ul class="side-nav">
             {{-- Menu --}}
             <li class="side-nav-title mt-2">Menu</li>
             <li class="side-nav-item">
                 <a href="{{ route('attendance.index') }}"
                     class="side-nav-link {{ request()->routeIs('attendance.index') ? 'active' : '' }}">
                     <span class="menu-icon"><i data-lucide="person-standing"></i></span>
                     <span class="menu-text">Kehadiran</span>
                 </a>
             </li>

             {{-- <li class="side-nav-item">
                 <a href="{{ route('penitipan-barang.index') }}"
                     class="side-nav-link {{ request()->routeIs('penitipan-barang.index') ? 'active' : '' }}">
                     <span class="menu-icon"><i data-lucide="package"></i></span>
                     <span class="menu-text">Penitipan Barang</span>
                 </a>
             </li> --}}

             <li class="side-nav-item">
                 <a href="{{ route('pengambilan-makan.index') }}"
                     class="side-nav-link {{ request()->routeIs('pengambilan-makan.index') ? 'active' : '' }}">
                     <span class="menu-icon"><i data-lucide="utensils"></i></span>
                     <span class="menu-text">Pengambilan Makan</span>
                 </a>
             </li>

             <li class="side-nav-item">
                 <a href="{{ route('pengambilan-kaos.index') }}"
                     class="side-nav-link {{ request()->routeIs('pengambilan-kaos.index') ? 'active' : '' }}">
                     <span class="menu-icon"><i data-lucide="shopping-bag"></i></span>
                     <span class="menu-text">Pengambilan Kaos</span>
                 </a>
             </li>

             <li class="side-nav-item">
                 <a href="{{ route('pengambilan-eskrim.index') }}"
                     class="side-nav-link {{ request()->routeIs('pengambilan-eskrim.index') ? 'active' : '' }}">
                     <span class="menu-icon"><i data-lucide="ice-cream"></i></span>
                     <span class="menu-text">Pengambilan Eskrim</span>
                 </a>
             </li>

             {{-- Master Data --}}
             <li class="side-nav-title mt-2">Master Data</li>
             <li class="side-nav-item">
                 <a href="{{ route('participants.index') }}"
                     class="side-nav-link {{ request()->routeIs('participants.index') ? 'active' : '' }}">
                     <span class="menu-icon"><i data-lucide="users"></i></span>
                     <span class="menu-text">Peserta</span>
                 </a>
             </li>

             <li class="side-nav-item">
                 <a href="{{ route('msid.index') }}"
                     class="side-nav-link {{ request()->routeIs('msid.index') ? 'active' : '' }}">
                     <span class="menu-icon"><i data-lucide="id-card"></i></span>
                     <span class="menu-text">MSID</span>
                 </a>
             </li>
             {{-- todo: kegiatan --}}
             {{-- <li class="side-nav-item">
                 <a href="{{ route('kegiatan.index') }}"
                     class="side-nav-link {{ request()->routeIs('kegiatan.index') ? 'active' : '' }}">
                     <span class="menu-icon"><i data-lucide="calendar"></i></span>
                     <span class="menu-text">Kegiatan</span>
                 </a>
             </li> --}}

         </ul>
     </div>
 </div>
