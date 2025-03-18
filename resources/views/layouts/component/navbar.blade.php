<nav class="app-header navbar navbar-expand bg-body">
     <div class="container-fluid">
       <ul class="navbar-nav">
         <li class="nav-item">
           <a class="nav-link" data-lte-toggle="sidebar" href="#" role="button">
             <i class="bi bi-list"></i>
           </a>
         </li>
       </ul>
       <ul class="navbar-nav ms-auto">
         <li class="nav-item">
           <a class="nav-link" href="#" data-lte-toggle="fullscreen">
             <i data-lte-icon="maximize" class="bi bi-arrows-fullscreen"></i>
             <i data-lte-icon="minimize" class="bi bi-fullscreen-exit" style="display: none"></i>
           </a>
         </li>
         <li class="nav-item dropdown user-menu">
           <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">
             <img
               src="{{ asset('assets/dist/assets/img/user2-160x160.jpg') }}"
               class="user-image rounded-circle shadow"
               alt="User Image"
             />
             <span class="d-none d-md-inline">
                {{ optional(auth()->user())->name ?? "User" }}
             </span>
           </a>
           <ul class="dropdown-menu dropdown-menu-lg dropdown-menu-end">
             <li class="user-header text-bg-primary">
               <img
                 src="{{ asset('assets/dist/assets/img/user2-160x160.jpg') }}"
                 class="rounded-circle shadow"
                 alt="User Image"
               />
               <p>
                 {{ optional(auth()->user())->name ?? "User" }}
                 <small>{{optional(auth()->user())->Email ?? "User"}}</small>
               </p>
             </li>

             <li class="user-footer">
               {{-- <a href="#" class="btn btn-default btn-flat">Profile</a> --}}
               <form action="{{ route('logout') }}" method="post">
                   @csrf
                   <button type="submit" class="btn btn-default btn-flat float-end" id="logoutBtn">Logout</button>
               </form>
             </li>
           </ul>
         </li>
       </ul>
     </div>
   </nav>