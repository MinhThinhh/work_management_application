@extends('layouts.app')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
    <style>
        /* Profile Button & Dropdown Styles */
        .profile-button {
            background: none;
            border: none;
            cursor: pointer;
            padding: 0;
            transition: transform 0.2s;
        }

        .profile-button:hover {
            transform: scale(1.05);
        }

        .profile-icon {
            width: 40px;
            height: 40px;
            background: #f0f0f0;
            color: #333;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
            font-weight: bold;
            text-transform: uppercase;
            border: 2px solid #e0e0e0;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .profile-dropdown {
            position: absolute;
            top: 50px;
            right: 0;
            background-color: white;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
            z-index: 1000;
            min-width: 200px;
            border: 1px solid #f0f0f0;
        }

        .password-change-container,
        .edit-profile-container {
            position: absolute;
            top: 0;
            right: 0;
            z-index: 1000;
            margin-top: 0;
            padding-top: 0;
        }

        .hidden {
            display: none;
        }
    </style>
@endsection

@section('content')

    <div id="jwt-status" class="hidden fixed top-0 right-0 m-4 p-3 rounded z-50" style="display: none;"></div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // JWT authentication is now handled by server-side middleware
            // No need to check localStorage token
            console.log('JWT authentication handled by server middleware');

            // Clear any existing localStorage token for security
            if (localStorage.getItem('jwt_token')) {
                localStorage.removeItem('jwt_token');
                console.log('Cleared localStorage token for security');
            }
        });
    </script>

    <div class="app">
        <div class="sidebar desktop-only">
            <div class="sidebar__logo">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-calendar">
                    <path d="M8 2v4" />
                    <path d="M16 2v4" />
                    <rect width="18" height="18" x="3" y="4" rx="2" />
                    <path d="M3 10h18" />
                </svg>
                <span class="sidebar__title">Vanilla Calendar</span>
            </div>



            <button class="button button--primary button--lg" data-event-create-button>
                Tạo công việc mới
            </button>

            <div class="mini-calendar" data-mini-calendar>
                <div class="mini-calendar__header">
                    <time class="mini-calendar__date" data-mini-calendar-date></time>

                    <div class="mini-calendar__controls">
                        <button class="button button--icon button--secondary button--sm" data-mini-calendar-previous-button>
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="button__icon">
                                <path d="m15 18-6-6 6-6" />
                            </svg>
                        </button>

                        <button class="button button--icon button--secondary button--sm" data-mini-calendar-next-button>
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="button__icon">
                                <path d="m9 18 6-6-6-6" />
                            </svg>
                        </button>
                    </div>
                </div>

                <div class="mini-calendar__content">
                    <ul class="mini-calendar__day-of-week-list">
                        <li class="mini-calendar__day-of-week">CN</li>
                        <li class="mini-calendar__day-of-week">T2</li>
                        <li class="mini-calendar__day-of-week">T3</li>
                        <li class="mini-calendar__day-of-week">T4</li>
                        <li class="mini-calendar__day-of-week">T5</li>
                        <li class="mini-calendar__day-of-week">T6</li>
                        <li class="mini-calendar__day-of-week">T7</li>
                    </ul>

                    <ul class="mini-calendar__day-list" data-mini-calendar-day-list></ul>
                </div>
            </div>
        </div>
        <main class="main">
            <div class="nav">
                <button class="button button--icon button--secondary mobile-only" data-hamburger-button>
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="button__icon">
                        <line x1="4" x2="20" y1="12" y2="12" />
                        <line x1="4" x2="20" y1="6" y2="6" />
                        <line x1="4" x2="20" y1="18" y2="18" />
                    </svg>
                </button>

                <div class="nav__date-info">
                    <div class="nav__controls">
                        <button class="button button--secondary desktop-only" data-nav-today-button>
                            Hôm nay
                        </button>
                        <button class="button button--icon button--secondary mobile-only" data-nav-today-button>
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="button__icon">
                                <path d="M8 2v4" />
                                <path d="M16 2v4" />
                                <rect width="18" height="18" x="3" y="4" rx="2" />
                                <path d="M3 10h18" />
                                <path d="M8 14h.01" />
                                <path d="M12 14h.01" />
                                <path d="M16 14h.01" />
                                <path d="M8 18h.01" />
                                <path d="M12 18h.01" />
                                <path d="M16 18h.01" />
                            </svg>
                        </button>

                        <div class="nav__arrows">
                            <button class="button button--icon button--secondary" data-nav-previous-button>
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="button__icon">
                                    <path d="m15 18-6-6 6-6" />
                                </svg>
                            </button>

                            <button class="button button--icon button--secondary" data-nav-next-button>
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="button__icon">
                                    <path d="m9 18 6-6-6-6" />
                                </svg>
                            </button>
                        </div>
                    </div>
                    <time class="nav__date" data-nav-date></time>
                </div>

                <div class="flex flex-col items-end ml-auto">
                    <div class="flex items-center mb-2">
                        <div class="relative">
                            <button id="profile-button" class="profile-button">
                                <div class="profile-icon">
                                    {{ substr(Auth::user()->name ?? Auth::user()->email, 0, 2) }}
                                </div>
                            </button>
                            <div id="profile-dropdown" class="profile-dropdown hidden">
                                @include('profile.profile-dropdown')
                            </div>
                            <div id="password-change-container" class="password-change-container hidden">
                                @include('profile.password-form')
                            </div>

                            <div id="edit-profile-container" class="edit-profile-container hidden">
                                @include('profile.edit-profile')
                            </div>
                        </div>
                    </div>

                    <div class="select desktop-only">
                        <select class="select__select" data-view-select>
                            <option value="day">Ngày</option>
                            <option value="week">Tuần</option>
                            <option value="month" selected>Tháng</option>
                        </select>

                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="select__icon">
                            <path d="m6 9 6 6 6-6" />
                        </svg>
                    </div>
                </div>

                <script>
                    // Xử lý hiển thị/ẩn profile dropdown
                    document.addEventListener('DOMContentLoaded', function() {
                        const profileButton = document.getElementById('profile-button');
                        const profileDropdown = document.getElementById('profile-dropdown');

                        // Hiển thị/ẩn dropdown khi nhấn vào profile button
                        profileButton.addEventListener('click', function(e) {
                            e.stopPropagation();
                            profileDropdown.classList.toggle('hidden');
                        });

                        // Ẩn dropdown khi nhấn ra ngoài
                        document.addEventListener('click', function(e) {
                            if (!profileDropdown.contains(e.target) && e.target !== profileButton) {
                                profileDropdown.classList.add('hidden');
                            }
                        });
                    });
                </script>
            </div>
            <div class="calendar" data-calendar></div>
        </main>
    </div>

    <button class="fab mobile-only" data-event-create-button>
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="fab__icon">
            <path d="M5 12h14" />
            <path d="M12 5v14" />
        </svg>
    </button>

    <dialog class="dialog" data-dialog="event-form">
        <form class="form" data-event-form>
            @csrf
            <div class="dialog__wrapper">
                <div class="dialog__header">
                    <h2 class="dialog__title" data-dialog-title></h2>
                    <button class="button button--icon button--secondary" type="button" data-dialog-close-button style="z-index: 1010;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="button__icon">
                            <path d="M18 6 6 18" />
                            <path d="m6 6 12 12" />
                        </svg>
                    </button>
                </div>

                <div class="dialog__content">
                    <div class="form__fields">
                        <input type="hidden" id="id" name="id" />

                        <div class="form__field">
                            <label class="form__label" for="title">Tiêu đề</label>
                            <input class="input input--fill" id="title" name="title" type="text" placeholder="Nhập tiêu đề công việc" required autofocus />
                        </div>

                        <div class="form__field">
                            <label class="form__label" for="description">Mô tả</label>
                            <textarea class="input input--fill" id="description" name="description" placeholder="Mô tả chi tiết công việc"></textarea>
                        </div>

                        <div class="form__field">
                            <label class="form__label" for="start_date">Ngày bắt đầu</label>
                            <input class="input input--fill" id="start_date" name="start_date" type="date" required />
                        </div>

                        <div class="form__field">
                            <label class="form__label" for="due_date">Ngày hết hạn</label>
                            <input class="input input--fill" id="due_date" name="due_date" type="date" required />
                        </div>

                        <div class="form__field">
                            <label class="form__label" for="priority">Mức độ ưu tiên</label>
                            <div class="select select--fill">
                                <select class="select__select" id="priority" name="priority">
                                    <option value="low">Thấp</option>
                                    <option value="medium" selected>Trung bình</option>
                                    <option value="high">Cao</option>
                                </select>
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="select__icon">
                                    <path d="m6 9 6 6 6-6" />
                                </svg>
                            </div>
                        </div>

                        <div class="form__field">
                            <label class="form__label" for="status">Trạng thái</label>
                            <div class="select select--fill">
                                <select class="select__select" id="status" name="status">
                                    <option value="pending" selected>Chờ xử lý</option>
                                    <option value="in_progress">Đang thực hiện</option>
                                    <option value="completed">Hoàn thành</option>
                                </select>
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="select__icon">
                                    <path d="m6 9 6 6 6-6" />
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="dialog__footer">
                    <div class="dialog__actions">
                        <button class="button button--secondary" type="button" data-dialog-close-button style="z-index: 1010;">
                            Hủy
                        </button>
                        <button type="submit" class="button button--primary" style="z-index: 1010;">Lưu</button>
                    </div>
                </div>
            </div>
        </form>
    </dialog>
@endsection

@section('scripts')
    <script src="{{ asset('js/dashboard.js') }}"></script>
@endsection