 <div class="col-md-3 left_col">
     <div class="left_col scroll-view">
         {{-- <div class="navbar nav_title" style="border: 0;">
             <a href="index.html" ></i> <span></span></a>
         </div> --}}

         <div class="clearfix"></div>

         <!-- menu profile quick info -->
         <div class="profile clearfix">

             <div class="profile_info">
                 <span>Xin chào,</span>
                 <h2></h2>
             </div>
         </div>
         <!-- /menu profile quick info -->

         <br />

         <!-- sidebar menu -->
         <div id="sidebar-menu" class="main_menu_side hidden-print main_menu">
             <div class="menu_section">
                 <h3>Tổng quan</h3>
                 @php
                     $adminUser = Auth::guard('admin')->user();
                 @endphp
                 <ul class="nav side-menu">
                     <li><a href="{{ route('admin.dashboard') }}"><i class="fa fa-home"></i> Dashboard </a>

                     </li>
                     @if ($adminUser->role->permissions->contains('name', 'manage_users'))
                         <li><a href="{{ route('admin.users.index') }}"><i class="fa fa-edit"></i> Quản lý người dùng
                             </a>

                         </li>
                     @endif

                     @if ($adminUser->role->permissions->contains('name', 'manage_categories'))
                         <li><a href="#"><i class="fa fa-lock"></i> Quản lý danh mục <span
                                     class="fa fa-chevron-down"></span></a>
                             <ul class="nav child_menu">
                                 <li><a href="{{ route('admin.categories.add') }}">Thêm danh mục</a></li>
                                 <li><a href="{{ route('admin.categories.index') }}">Danh sách danh mục</a></li>

                             </ul>
                         </li>
                     @endif


                     @if ($adminUser->role->permissions->contains('name', 'manage_products'))
                         <li><a href="#"><i class="fa fa-desktop"></i> Quản lý sản phẩm <span
                                     class="fa fa-chevron-down"></span></a>
                             <ul class="nav child_menu">
                                 <li><a href="{{ route('admin.product.add') }}">Thêm sản phẩm</a></li>
                                 <li><a href="{{ route('admin.products.index') }}">Danh sách sản phẩm</a></li>

                             </ul>
                         </li>
                     @endif


                     @if ($adminUser->role->permissions->contains('name', 'manage_variants'))
                         <li><a href="#"><i class="fa fa-cubes"></i> Quản lý biến thể <span
                                     class="fa fa-chevron-down"></span></a>
                             <ul class="nav child_menu">
                                 <li><a href="{{ route('admin.variants.all') }}">Danh sách biến thể</a></li>
                             </ul>
                         </li>
                     @endif


                     @if ($adminUser->role->permissions->contains('name', 'manage_suppliers'))
                         <li><a href="#"><i class="fa fa-truck"></i> Quản lý nhà cung cấp <span
                                     class="fa fa-chevron-down"></span></a>
                             <ul class="nav child_menu">
                                 <li><a href="{{ route('admin.supplier.add') }}">Thêm nhà cung cấp</a></li>
                                 <li><a href="{{ route('admin.suppliers.index') }}">Danh sách nhà cung cấp</a></li>
                             </ul>
                         </li>
                     @endif

                     @if ($adminUser->role->permissions->contains('name', 'manage_orders'))
                         <li><a href="{{ route('admin.orders.index') }}"><i class="fa fa-edit"></i> Quản lý đơn hàng
                             </a>

                         </li>
                     @endif

                     {{-- Quản lý khuyến mãi --}}
                     @if ($adminUser->role->permissions->contains('name', 'manage_coupons'))
                         <li><a href="{{ route('coupons.index') }}"><i class="fa fa-gift"></i> Quản lý khuyến mãi </a>
                         </li>
                     @endif

                     {{-- contact --}}

                     @if ($adminUser->role->permissions->contains('name', 'manage_contacts'))
                         <li><a href="{{ route('admin.contacts.index') }}"><i class="fa fa-edit"></i> Liên hệ </a>


                         </li>
                     @endif

                     @if ($adminUser->role->permissions->contains('name', 'manage_deliveries'))
                         <li>
                             <a href="#"><i class="fa fa-truck"></i> Giao hàng <span
                                     class="fa fa-chevron-down"></span></a>
                             <ul class="nav child_menu">
                                 <li><a href="{{ route('admin.deliveries.dashboard') }}">Dashboard</a></li>
                                 <li><a href="{{ route('admin.deliveries.myOrders') }}">Đơn hàng của tôi</a></li>
                                 @if ($adminUser->role->name === 'admin')
                                     <li><a href="{{ route('admin.deliveries.index') }}">Phân công giao hàng</a></li>
                                 @endif
                             </ul>
                         </li>
                     @endif
                     {{-- QUản lý hoàn trả --}}

                     {{-- Quản lý hoàn trả --}}
                     @if ($adminUser->role->permissions->contains('name', 'manage_refunds'))
                         <li class="nav-item">
                             <a class="nav-link" href="{{ route('admin.refunds.index') }}">
                                 <i class="fa fa-undo"></i>
                                 <span>Yêu cầu hoàn trả</span>
                             </a>
                         </li>
                     @endif

                 </ul>
             </div>


         </div>
         <!-- /sidebar menu -->

         <!-- /menu footer buttons -->
         <div class="sidebar-footer hidden-small">
             <a data-toggle="tooltip" data-placement="top" title="Đăng xuất" href="{{ route('admin.logout') }}">
                 <span class="glyphicon glyphicon-off" aria-hidden="true"></span>
             </a>
         </div>
         <!-- /menu footer buttons -->
     </div>
 </div>
