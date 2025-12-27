  <!-- top navigation -->
  <div class="top_nav">
      <div class="nav_menu">
          <div class="nav toggle">
              <a id="menu_toggle"><i class="fa fa-bars"></i></a>
          </div>
          <nav class="nav navbar-nav">
              <ul class=" navbar-right">
                  <li class="nav-item dropdown open" style="padding-left: 15px;">

                      <img src="images/img.jpg" alt="">
                      </a>

                  </li>


                  <li  class="nav-item dropdown open">
                      <a href="javascript:;" class="dropdown-toggle info-number" 
                          data-toggle="dropdown" aria-expanded="false">
                          <i class="fa fa-bell-o"></i>
                          <span class="badge bg-green">{{ $notifications->count() }}</span>
                      </a>
                      <ul class="dropdown-menu list-unstyled msg_list" role="menu" aria-labelledby="navbarDropdown1">
                            @foreach ($notifications as $notification)
                            <li class="nav-item">
                                <a class="dropdown-item" href="{{ url('admin' . $notification->link) }}">
                                 
                                    <span>
                                        <span>{{ $notification->title }}</span>
                                        <span class="time">{{ \Carbon\Carbon::parse($notification->created_at)->format('d / m / Y') }}</span>
                                    </span>
                                    <span class="message">
                                        {{ Str::limit( $notification->message, 50) }}
                                    </span>
                                </a>
                            </li>
                            @endforeach
                          <li class="nav-item">
                              <div class="text-center">
                                  <a class="dropdown-item" href="{{ route('admin.notifications.index') }}">
                                      <strong>Xem tất cả thông báo</strong>
                                      <i class="fa fa-angle-right"></i>
                                  </a>
                              </div>
                          </li>
                      </ul>
                  </li>
              </ul>
          </nav>
      </div>
  </div>
  <!-- /top navigation -->
