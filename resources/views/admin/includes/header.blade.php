 <div class="nk-header nk-header-fixed is-light">
     <div class="container-fluid">
         <div class="nk-header-wrap">
             <div class="nk-menu-trigger d-xl-none ms-n1"><a href="#" class="nk-nav-toggle nk-quick-nav-icon"
                     data-target="sidebarMenu">
                     <svg xmlns="http://www.w3.org/2000/svg" style="width: 26px" fill="currentColor" class="bi bi-list"
                         viewBox="0 0 16 16">
                         <path fill-rule="evenodd"
                             d="M2.5 12a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5zm0-4a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5zm0-4a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5z" />
                     </svg>
                 </a>
             </div>
             <div class="nk-header-brand d-xl-none">
                 <a href="{{ route('admin.dashboard') }}" class="logo-link">
                     <img class="mx-1" src="{{ asset('assets/images/logo.jpg') }}"
                         style="width:12rem;object-fit:contain" alt="logo" />
                     <div class="flex-grow-1 d-flex  align-items-center">
                         <div class="safa-logo-text ms-2" style="color:#ffb800;font-size:35px;font-weight:600">
                         </div>
                     </div>
                 </a>
             </div>
             <div class="nk-header-tools">
                 <ul class="nk-quick-nav">
                     <li class="dropdown user-dropdown">
                         <a href="#" class="dropdown-toggle me-n1" data-bs-toggle="dropdown">
                             <div class="user-toggle">
                                 <div class="user-avatar sm"><em class="icon ni ni-user-alt"></em>
                                 </div>
                                 <div class="user-info d-none d-xl-block">
                                     <div class="user-status user-status-active" style="color:#ffb800">
                                         {{ Auth::user()->roles['0']->name }}
                                     </div>
                                     <div class="user-name dropdown-indicator">{{ Auth::user()->name }}</div>
                                 </div>
                             </div>
                         </a>
                         <div class="dropdown-menu dropdown-menu-md dropdown-menu-end">
                             <div class="dropdown-inner user-card-wrap bg-lighter d-none d-md-block">
                                 <div class="user-card">
                                     <div class="user-info">
                                         <span class="lead-text">{{ Auth::user()->name }}</span>
                                         <span class="sub-text">{{ Auth::user()->email }}</span>
                                     </div>
                                 </div>
                             </div>
                             <div class="dropdown-inner">
                                 @role('Restaurant Owner')
                                     @impersonating
                                         <ul class="link-list">
                                             <li>
                                                 <form action="{{ route('impersonate.leave') }}" id="myform" id="myform">
                                                     @csrf
                                                     <a type="submit" onclick="document.getElementById('myform').submit()"
                                                         data-toggle="tooltip" data-placement="top" title="Go back to Admin">
                                                         <em class="icon ni ni-signout"></em>
                                                         <span class=" "> Sign out Restaurant Owner</span></a>
                                                 </form>
                                             </li>
                                         </ul>
                                     @endImpersonating
                                 @endrole
                                 <ul class="link-list">
                                     <li>
                                         <form action="{{ route('logout') }}" method="POST" id="myform"
                                             id="myform">
                                             @csrf
                                             <a type="submit" onclick="document.getElementById('myform').submit()"
                                                 data-toggle="tooltip" data-placement="top" title="Sign Out Dashboard">
                                                 <em class="icon ni ni-signout"></em>
                                                 <span class=" "> Sign out</span></a>
                                         </form>
                                     </li>
                                 </ul>
                             </div>
                         </div>
                     </li>
                 </ul>
             </div>
         </div>
     </div>
 </div>


