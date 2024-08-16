
                    <div id="kt_header" class="kt-header kt-grid__item kt-header--fixed">
                        <!-- begin:: Header Menu -->
                        <button class="kt-header-menu-wrapper-close" id="kt_header_menu_mobile_close_btn"><i class="la la-close"></i></button>
                        <div class="kt-header-menu-wrapper" id="kt_header_menu_wrapper">
                            <div id="kt_header_menu" class="kt-header-menu kt-header-menu-mobile kt-header-menu--layout-default">
                            <ul class="kt-menu__nav">
                            @if(\Auth::user()->instances()->active()->first())

                                <li class="kt-menu__item kt-menu__item--active" aria-haspopup="true">
                                    <a href="{{route('labs.show', \Auth::user()->instances()->active()->first()->machine_id)}}" class="kt-menu__link" title="Máquina ativa no momento.">
                                    <span class="kt-menu__link-icon svg-icon svg-icon-primary svg-icon-2x"><!--begin::Svg Icon | path:/var/www/preview.keenthemes.com/metronic/releases/2020-12-28-020759/theme/html/demo1/dist/../src/media/svg/icons/Layout/Layout-4-blocks.svg--><svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                        <rect x="0" y="0" width="24" height="24"/>
                                        <rect fill="#000000" x="4" y="4" width="7" height="7" rx="1.5"/>
                                        <path d="M5.5,13 L9.5,13 C10.3284271,13 11,13.6715729 11,14.5 L11,18.5 C11,19.3284271 10.3284271,20 9.5,20 L5.5,20 C4.67157288,20 4,19.3284271 4,18.5 L4,14.5 C4,13.6715729 4.67157288,13 5.5,13 Z M14.5,4 L18.5,4 C19.3284271,4 20,4.67157288 20,5.5 L20,9.5 C20,10.3284271 19.3284271,11 18.5,11 L14.5,11 C13.6715729,11 13,10.3284271 13,9.5 L13,5.5 C13,4.67157288 13.6715729,4 14.5,4 Z M14.5,13 L18.5,13 C19.3284271,13 20,13.6715729 20,14.5 L20,18.5 C20,19.3284271 19.3284271,20 18.5,20 L14.5,20 C13.6715729,20 13,19.3284271 13,18.5 L13,14.5 C13,13.6715729 13.6715729,13 14.5,13 Z" fill="#000000" opacity="0.3"/>
                                    </g>
                                </svg><!--end::Svg Icon--></span>

                                     
                                        <span class="kt-menu__link-text">{{\Auth::user()->getCurrentMachineName()}}</span>
                                    </a>
                                
                                </li>
                                @endif
                             
                             
                            </ul>
                            </div>
                        </div>
                        
                        <!-- end:: Header Menu -->
            
                        <!-- begin:: Header Topbar -->
                        <div class="kt-header__topbar">
                            <!--begin: User Bar -->
                            <div class="kt-header__topbar-item kt-header__topbar-item--user">
                                <div class="kt-header__topbar-wrapper" data-toggle="dropdown" data-offset="0px,0px">
                                    <div class="kt-header__topbar-user">
                                        <span class="kt-header__topbar-welcome kt-hidden-mobile"> 
										
										<b>	Olá</b>,</span>
                                        <span class="kt-header__topbar-username kt-hidden-mobile">{{explode(" ", Auth::user()->name)[0]}}</span>
                                        <img class="" alt="Pic" src="https://robohash.org/{{md5(Auth::user()->email)}}" />

                                        <!--use below badge element instead the user avatar to display username's first letter(remove kt-hidden class to display it) -->
                                        <!--<span class="kt-badge kt-badge--username kt-badge--unified-success kt-badge--lg kt-badge--rounded kt-badge--bold">S</span>-->
                                    </div>
                                </div>
                                <div class="dropdown-menu dropdown-menu-fit dropdown-menu-right dropdown-menu-anim dropdown-menu-top-unround dropdown-menu-xl">
                                    <!--begin: Head -->
                                    <div class="kt-user-card kt-user-card--skin-dark kt-notification-item-padding-x" style="background-image: url({{ asset('assets/media/misc/bg-1.jpg')}});">
                                        <div class="kt-user-card__avatar">
                                            <img class="" alt="Pic" src="https://robohash.org/{{md5(Auth::user()->email)}}" />

                                            <!--use below badge element instead the user avatar to display username's first letter(remove kt-hidden class to display it) -->
                                            <!--<span class="kt-badge kt-badge--lg kt-badge--rounded kt-badge--bold kt-font-success">S</span>-->
                                        </div>
                                        <div class="kt-user-card__name">
                                            {{Auth::user()->name}}
                                        </div>
                                        <div class="kt-user-card__badge">
                                            <!--<span class="btn btn-success btn-sm btn-bold btn-font-md"></span>-->
                                        </div>
                                    </div>

                                    <!--end: Head -->

                                    <!--begin: Navigation -->
                                    <div class="kt-notification">
                                        <a href="/profile" class="kt-notification__item">
                                            <div class="kt-notification__item-icon">
                                                <i class="flaticon2-calendar-3 kt-font-success"></i>
                                            </div>
                                            <div class="kt-notification__item-details">
                                                <div class="kt-notification__item-title kt-font-bold">
                                                    Meu perfil
                                                </div>
                                                <div class="kt-notification__item-time">
                                                    Configurações da conta e outros...
                                                </div>
                                            </div>
                                        </a>

                                        <a href="/dashboard#hacktivity" class="kt-notification__item">
                                            <div class="kt-notification__item-icon">
                                                <i class="flaticon2-rocket-1 kt-font-danger"></i>
                                            </div>
                                            <div class="kt-notification__item-details">
                                                <div class="kt-notification__item-title kt-font-bold">
                                                    Hacktivity
                                                </div>
                                                <div class="kt-notification__item-time">
                                                    Progresso e Timeline
                                                </div>
                                            </div>
                                        </a>
                                        <a href="/learn" class="kt-notification__item">
                                            <div class="kt-notification__item-icon">
                                                <i class="flaticon2-hourglass kt-font-brand"></i>
                                            </div>
                                            <div class="kt-notification__item-details">
                                                <div class="kt-notification__item-title kt-font-bold">
                                                    Learn
                                                </div>
                                                <div class="kt-notification__item-time">
                                                    Aulas e Tarefas
                                                </div>
                                            </div>
                                        </a>
                                        <a href="/profile/billing" class="kt-notification__item">
                                            <div class="kt-notification__item-icon">
                                                <i class="flaticon2-cardiogram kt-font-warning"></i>
                                            </div>
                                            <div class="kt-notification__item-details">
                                                <div class="kt-notification__item-title kt-font-bold">
                                                    Billing
                                                </div>
                                                <div class="kt-notification__item-time">
                                                    Assinatura e informações de pagamento
                                                </div>
                                            </div>
                                        </a>
                                        <div class="kt-notification__custom kt-space-between">
                                            <form action="/logout" method="POST">
                                            {{ csrf_field() }}
                                            <button type='submit' class="btn btn-label btn-label-brand btn-sm btn-bold">Logout</button>
                                            </form>
                                        </div>
                                    </div>

                                    <!--end: Navigation -->
                                </div>
                            </div>

                            <!--end: User Bar -->
                        </div>

                        <!-- end:: Header Topbar -->
                    </div>