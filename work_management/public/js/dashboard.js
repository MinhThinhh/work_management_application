// Dashboard JavaScript
document.addEventListener('DOMContentLoaded', function() {
    // Kiểm tra xác thực
    checkAuthentication();

    // Initialize calendar
    initCalendar();

    // Setup event handlers
    setupEventHandlers();
});

// Kiểm tra xác thực
function checkAuthentication() {
    // JWT authentication is now handled by server-side middleware
    // No need to check localStorage token
    console.log('JWT authentication handled by server middleware');

    // Ensure credentials are sent with fetch requests
    const originalFetch = window.fetch;
    window.fetch = function(url, options = {}) {
        options.credentials = options.credentials || 'same-origin';
        return originalFetch(url, options);
    };
}

// Calendar state
const calendarState = {
    currentDate: new Date(),
    currentView: 'month', // 'day', 'week', 'month'
    events: [],
    selectedEvent: null
};

// Initialize calendar
function initCalendar() {
    // Update date displays
    updateDateDisplay();

    // Khởi tạo mảng events rỗng
    calendarState.events = [];

    // Render calendar based on current view
    renderCalendar();

    // Fetch events from API
    fetchEvents();
}

// Update all date displays
function updateDateDisplay() {
    // Update main navigation date
    const navDateElement = document.querySelector('[data-nav-date]');
    if (navDateElement) {
        navDateElement.textContent = formatDateForNav(calendarState.currentDate);
    }

    // Update mini calendar date
    const miniCalendarDateElement = document.querySelector('[data-mini-calendar-date]');
    if (miniCalendarDateElement) {
        miniCalendarDateElement.textContent = formatDateForMiniCalendar(calendarState.currentDate);
    }

    // Render mini calendar days
    renderMiniCalendarDays();
}

// Format date for main navigation
function formatDateForNav(date) {
    const options = {
        month: 'long',
        year: 'numeric'
    };

    if (calendarState.currentView === 'day') {
        options.day = 'numeric';
    } else if (calendarState.currentView === 'week') {
        // For week view, show range
        const startOfWeek = getStartOfWeek(date);
        const endOfWeek = new Date(startOfWeek);
        endOfWeek.setDate(startOfWeek.getDate() + 6);

        if (startOfWeek.getMonth() === endOfWeek.getMonth()) {
            return `${startOfWeek.getDate()} - ${endOfWeek.getDate()} ${startOfWeek.toLocaleDateString('vi-VN', { month: 'long', year: 'numeric' })}`;
        } else {
            return `${startOfWeek.getDate()} ${startOfWeek.toLocaleDateString('vi-VN', { month: 'short' })} - ${endOfWeek.getDate()} ${endOfWeek.toLocaleDateString('vi-VN', { month: 'short', year: 'numeric' })}`;
        }
    }

    return date.toLocaleDateString('vi-VN', options);
}

// Format date for mini calendar
function formatDateForMiniCalendar(date) {
    return date.toLocaleDateString('vi-VN', { month: 'long', year: 'numeric' });
}

// Get start of week (Sunday)
function getStartOfWeek(date) {
    const result = new Date(date);
    const day = result.getDay();
    result.setDate(result.getDate() - day);
    return result;
}

// Render mini calendar days
function renderMiniCalendarDays() {
    const dayListElement = document.querySelector('[data-mini-calendar-day-list]');
    if (!dayListElement) return;

    // Clear existing days
    dayListElement.innerHTML = '';

    // Get current month's first day and last day
    const currentYear = calendarState.currentDate.getFullYear();
    const currentMonth = calendarState.currentDate.getMonth();
    const firstDay = new Date(currentYear, currentMonth, 1);
    const lastDay = new Date(currentYear, currentMonth + 1, 0);

    // Get the day of the week for the first day (0 = Sunday, 6 = Saturday)
    const firstDayOfWeek = firstDay.getDay();

    // Create days from previous month to fill the first row
    const prevMonthLastDay = new Date(currentYear, currentMonth, 0).getDate();
    for (let i = 0; i < firstDayOfWeek; i++) {
        const day = prevMonthLastDay - firstDayOfWeek + i + 1;
        const prevMonth = currentMonth === 0 ? 11 : currentMonth - 1;
        const prevYear = currentMonth === 0 ? currentYear - 1 : currentYear;
        const date = new Date(prevYear, prevMonth, day);

        const dayElement = createDayElement(day, 'prev-month', isDateToday(date), false, date);
        dayListElement.appendChild(dayElement);
    }

    // Create days for current month
    for (let i = 1; i <= lastDay.getDate(); i++) {
        const date = new Date(currentYear, currentMonth, i);
        const isToday = isDateToday(date);
        const isSelected = isSameDay(date, calendarState.currentDate);

        const dayElement = createDayElement(i, 'current-month', isToday, isSelected, date);
        dayListElement.appendChild(dayElement);
    }

    // Calculate how many days from next month we need to show
    const daysFromNextMonth = 42 - (firstDayOfWeek + lastDay.getDate());

    // Create days from next month to fill the last row(s)
    for (let i = 1; i <= daysFromNextMonth; i++) {
        const nextMonth = currentMonth === 11 ? 0 : currentMonth + 1;
        const nextYear = currentMonth === 11 ? currentYear + 1 : currentYear;
        const date = new Date(nextYear, nextMonth, i);

        const dayElement = createDayElement(i, 'next-month', isDateToday(date), false, date);
        dayListElement.appendChild(dayElement);
    }
}

// Create a day element for mini calendar
function createDayElement(day, monthClass, isToday = false, isSelected = false, date = null) {
    const dayElement = document.createElement('li');
    dayElement.textContent = day;
    dayElement.classList.add('mini-calendar__day');

    if (monthClass) {
        dayElement.classList.add(monthClass);
    }

    if (isToday) {
        dayElement.classList.add('today');
    }

    if (isSelected) {
        dayElement.classList.add('selected');
    }

    // Add click event to day if date is provided
    if (date) {
        dayElement.addEventListener('click', () => {
            // Remove selected class from all days
            const allDays = document.querySelectorAll('.mini-calendar__day');
            allDays.forEach(day => day.classList.remove('selected'));

            // Add selected class to clicked day
            dayElement.classList.add('selected');

            // Update selected date
            calendarState.currentDate = new Date(date);

            // Update calendar
            updateDateDisplay();
            renderCalendar();
        });
    }

    return dayElement;
}

// Check if date is today
function isDateToday(date) {
    const today = new Date();
    return date.getDate() === today.getDate() &&
           date.getMonth() === today.getMonth() &&
           date.getFullYear() === today.getFullYear();
}

// Check if two dates are the same day
function isSameDay(date1, date2) {
    return date1.getDate() === date2.getDate() &&
           date1.getMonth() === date2.getMonth() &&
           date1.getFullYear() === date2.getFullYear();
}

// Render main calendar based on current view
function renderCalendar() {
    const calendarElement = document.querySelector('[data-calendar]');
    if (!calendarElement) return;

    // Clear existing content
    calendarElement.innerHTML = '';

    // Render based on current view
    if (calendarState.currentView === 'month') {
        renderMonthView(calendarElement);
    } else if (calendarState.currentView === 'week') {
        renderWeekView(calendarElement);
    } else if (calendarState.currentView === 'day') {
        renderDayView(calendarElement);
    }
}

// Render month view
function renderMonthView(calendarElement) {
    // Create month grid container
    const monthGrid = document.createElement('div');
    monthGrid.classList.add('month-grid');

    // Add day of week headers
    const daysOfWeek = ['CN', 'T2', 'T3', 'T4', 'T5', 'T6', 'T7'];
    daysOfWeek.forEach(day => {
        const dayHeader = document.createElement('div');
        dayHeader.classList.add('month-grid__header');
        dayHeader.textContent = day;
        monthGrid.appendChild(dayHeader);
    });

    // Get current month's first day and last day
    const currentYear = calendarState.currentDate.getFullYear();
    const currentMonth = calendarState.currentDate.getMonth();
    const firstDay = new Date(currentYear, currentMonth, 1);
    const lastDay = new Date(currentYear, currentMonth + 1, 0);

    // Get the day of the week for the first day (0 = Sunday, 6 = Saturday)
    const firstDayOfWeek = firstDay.getDay();

    // Create days from previous month to fill the first row
    const prevMonthLastDay = new Date(currentYear, currentMonth, 0).getDate();
    for (let i = 0; i < firstDayOfWeek; i++) {
        const day = prevMonthLastDay - firstDayOfWeek + i + 1;
        const prevMonth = currentMonth === 0 ? 11 : currentMonth - 1;
        const prevYear = currentMonth === 0 ? currentYear - 1 : currentYear;
        const date = new Date(prevYear, prevMonth, day);
        const dayElement = createMonthDayElement(day, 'prev-month', isDateToday(date), date);
        monthGrid.appendChild(dayElement);
    }

    // Create days for current month
    for (let i = 1; i <= lastDay.getDate(); i++) {
        const date = new Date(currentYear, currentMonth, i);
        const isToday = isDateToday(date);
        const dayElement = createMonthDayElement(i, 'current-month', isToday, date);
        monthGrid.appendChild(dayElement);
    }

    // Calculate how many days from next month we need to show
    const daysFromNextMonth = 42 - (firstDayOfWeek + lastDay.getDate());

    // Create days from next month to fill the last row(s)
    for (let i = 1; i <= daysFromNextMonth; i++) {
        const nextMonth = currentMonth === 11 ? 0 : currentMonth + 1;
        const nextYear = currentMonth === 11 ? currentYear + 1 : currentYear;
        const date = new Date(nextYear, nextMonth, i);
        const dayElement = createMonthDayElement(i, 'next-month', isDateToday(date), date);
        monthGrid.appendChild(dayElement);
    }

    // Add month grid to calendar
    calendarElement.appendChild(monthGrid);

    // Debug
    console.log('Calendar events before adding to month view:', calendarState.events);

    // Add events to the calendar
    addEventsToCalendar();
}

// Create a day element for month view
function createMonthDayElement(day, monthClass, isToday = false, date = null) {
    const dayElement = document.createElement('div');
    dayElement.classList.add('month-grid__day');

    if (monthClass) {
        dayElement.classList.add(monthClass);
    }

    if (isToday) {
        dayElement.classList.add('today');
    }

    // Create day number
    const dayNumber = document.createElement('div');
    dayNumber.classList.add('month-grid__day-number');
    dayNumber.textContent = day;
    dayElement.appendChild(dayNumber);

    // Create events container
    const eventsContainer = document.createElement('div');
    eventsContainer.classList.add('month-grid__events');
    dayElement.appendChild(eventsContainer);

    // Store date in data attribute for later use
    if (date) {
        const dateStr = formatDateForInput(date);
        dayElement.dataset.date = dateStr;

        // Add click event to open create event dialog with this date
        dayElement.addEventListener('click', () => {
            calendarState.currentDate = new Date(date);
            openCreateEventDialog();
        });
    }

    return dayElement;
}

// Add events to the calendar
function addEventsToCalendar() {
    if (!calendarState.events || calendarState.events.length === 0) {
        return;
    }

    // Group events by date
    const eventsByDate = {};

    calendarState.events.forEach(event => {
        // Handle different date formats
        let eventDate;
        if (event.date) {
            eventDate = event.date;
        } else if (event.due_date) {
            eventDate = event.due_date;
        } else if (event.start_date) {
            eventDate = event.start_date;
        } else if (event.start) {
            eventDate = event.start.split('T')[0];
        } else {
            return; // Skip events without date
        }

        // Debug
        console.log('Event date:', eventDate, 'for event:', event.title);

        if (!eventsByDate[eventDate]) {
            eventsByDate[eventDate] = [];
        }

        eventsByDate[eventDate].push(event);
    });

    // Add events to day elements
    Object.keys(eventsByDate).forEach(date => {
        const dayElement = document.querySelector(`.month-grid__day[data-date="${date}"]`);
        if (!dayElement) return;

        const eventsContainer = dayElement.querySelector('.month-grid__events');
        if (!eventsContainer) return;

        // Limit to 3 events per day in month view
        const visibleEvents = eventsByDate[date].slice(0, 3);
        const remainingCount = eventsByDate[date].length - visibleEvents.length;

        visibleEvents.forEach(event => {
            const eventElement = document.createElement('div');
            eventElement.classList.add('month-grid__event');

            // Set background color based on priority or event color
            let bgColor = '#3490dc'; // Default blue

            if (event.color) {
                bgColor = event.color;
            } else if (event.priority) {
                switch(event.priority) {
                    case 'high':
                        bgColor = '#f56565'; // Red for high priority
                        break;
                    case 'medium':
                        bgColor = '#ed8936'; // Orange for medium priority
                        break;
                    case 'low':
                        bgColor = '#48bb78'; // Green for low priority
                        break;
                }
            }

            eventElement.style.backgroundColor = bgColor;
            eventElement.textContent = event.title;

            // Add click event to show event details
            eventElement.addEventListener('click', (e) => {
                e.stopPropagation(); // Prevent day click event
                showEventDetails(event);
            });

            eventsContainer.appendChild(eventElement);
        });

        // Show remaining count if needed
        if (remainingCount > 0) {
            const moreElement = document.createElement('div');
            moreElement.classList.add('month-grid__more-events');
            moreElement.textContent = `+${remainingCount} more`;

            moreElement.addEventListener('click', (e) => {
                e.stopPropagation(); // Prevent day click event
                showDayEvents(date, eventsByDate[date]);
            });

            eventsContainer.appendChild(moreElement);
        }
    });
}

// Show event details
function showEventDetails(event) {
    // Tạo overlay trước khi hiển thị modal
    createOverlay();

    // Create a modal to show event details
    const modal = document.createElement('div');
    modal.classList.add('event-details-modal');

    const modalContent = document.createElement('div');
    modalContent.classList.add('event-details-modal__content');

    // Create close button
    const closeButton = document.createElement('button');
    closeButton.classList.add('event-details-modal__close');
    closeButton.innerHTML = '&times;';
    closeButton.addEventListener('click', () => {
        document.body.removeChild(modal);
        removeOverlay(); // Xóa overlay khi đóng modal
    });

    // Create title
    const title = document.createElement('h2');
    title.classList.add('event-details-modal__title');
    title.textContent = event.title;

    // Create details
    const details = document.createElement('div');
    details.classList.add('event-details-modal__details');

    // Add date
    const dateElement = document.createElement('p');
    dateElement.innerHTML = `<strong>Ngày:</strong> ${formatDateForDisplay(event.date || event.due_date || event.start)}`;
    details.appendChild(dateElement);

    // Add description if available
    if (event.description) {
        const descElement = document.createElement('p');
        descElement.innerHTML = `<strong>Mô tả:</strong> ${event.description}`;
        details.appendChild(descElement);
    }

    // Add priority if available
    if (event.priority) {
        const priorityElement = document.createElement('p');
        priorityElement.innerHTML = `<strong>Mức độ ưu tiên:</strong> ${
            event.priority === 'high' ? 'Cao' :
            event.priority === 'medium' ? 'Trung bình' : 'Thấp'
        }`;
        details.appendChild(priorityElement);
    }

    // Add status if available
    if (event.status) {
        const statusElement = document.createElement('p');
        statusElement.innerHTML = `<strong>Trạng thái:</strong> ${
            event.status === 'completed' ? 'Hoàn thành' :
            event.status === 'in_progress' ? 'Đang thực hiện' : 'Chờ xử lý'
        }`;
        details.appendChild(statusElement);
    }

    // Add action buttons
    const actions = document.createElement('div');
    actions.classList.add('event-details-modal__actions');

    // Edit button
    const editButton = document.createElement('button');
    editButton.classList.add('button', 'button--primary');
    editButton.textContent = 'Chỉnh sửa';
    editButton.addEventListener('click', () => {
        // Redirect to edit page
        window.location.href = `/tasks/${event.id}/edit`;
    });
    actions.appendChild(editButton);

    // Delete button
    const deleteButton = document.createElement('button');
    deleteButton.classList.add('button', 'button--danger');
    deleteButton.textContent = 'Xóa';
    deleteButton.addEventListener('click', () => {
        if (confirm('Bạn có chắc chắn muốn xóa công việc này?')) {
            deleteEvent(event.id);
            document.body.removeChild(modal);
        }
    });
    actions.appendChild(deleteButton);

    // Assemble modal
    modalContent.appendChild(closeButton);
    modalContent.appendChild(title);
    modalContent.appendChild(details);
    modalContent.appendChild(actions);
    modal.appendChild(modalContent);

    // Add modal to body
    document.body.appendChild(modal);

    // Close modal when clicking outside
    modal.addEventListener('click', (e) => {
        if (e.target === modal) {
            document.body.removeChild(modal);
        }
    });
}

// Format date for display
function formatDateForDisplay(dateStr) {
    if (!dateStr) return '';

    const date = new Date(dateStr);
    return date.toLocaleDateString('vi-VN', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });
}

// Delete event
function deleteEvent(id) {
    if (!id) return;

    // Xác nhận trước khi xóa
    if (!confirm('Bạn có chắc chắn muốn xóa công việc này không?')) {
        return;
    }

    // Lấy CSRF token từ meta tag
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

    // Tạo form data với _method=DELETE để Laravel hiểu đây là request DELETE
    const formData = new FormData();
    formData.append('_method', 'DELETE');

    console.log('Deleting task with ID:', id);

    fetch(`/tasks/${id}`, {
        method: 'POST', // Sử dụng POST với _method=DELETE
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': csrfToken || ''
        },
        credentials: 'same-origin'
    })
    .then(response => {
        if (!response.ok) {
            // Tạo một đối tượng lỗi với response để xử lý sau
            const error = new Error('Lỗi khi xóa công việc: ' + response.status);
            error.response = response;
            throw error;
        }

        // Kiểm tra content-type
        const contentType = response.headers.get('content-type');
        if (contentType && contentType.includes('application/json')) {
            return response.json();
        } else {
            // Nếu không phải JSON, trả về object đơn giản
            return { success: true, message: 'Công việc đã được xóa thành công!' };
        }
    })
    .then(data => {
        console.log('Công việc đã được xóa:', data);

        // Hiển thị thông báo thành công
        showNotification('Công việc đã được xóa thành công!', 'success');

        // Đóng modal nếu đang mở
        const eventDetailsModal = document.querySelector('.event-details-modal');
        if (eventDetailsModal) {
            eventDetailsModal.remove();
        }

        // Cập nhật lại calendar
        fetchEvents();
    })
    .catch(error => {
        console.error('Lỗi khi xóa công việc:', error);

        // Xử lý phản hồi lỗi từ server
        if (error.response) {
            // Lỗi validation
            error.response.json().then(data => {
                let errorMessage = 'Lỗi khi xóa công việc: ';
                if (data.errors) {
                    // Lấy tất cả các lỗi validation
                    const errorMessages = Object.values(data.errors).flat();
                    errorMessage += errorMessages.join(', ');
                } else if (data.message) {
                    errorMessage += data.message;
                } else {
                    errorMessage += 'Không thể xóa công việc';
                }
                showNotification(errorMessage, 'error');
            }).catch(jsonError => {
                console.error('Lỗi khi parse JSON:', jsonError);
                showNotification('Lỗi khi xóa công việc: Không thể xóa công việc', 'error');
            });
        } else {
            // Hiển thị thông báo lỗi chung
            showNotification('Lỗi khi xóa công việc: ' + error.message, 'error');
        }
    });
}

// Show all events for a specific day
function showDayEvents(date, events) {
    // Tạo overlay trước khi hiển thị modal
    createOverlay();

    // Create a modal to show all events for the day
    const modal = document.createElement('div');
    modal.classList.add('day-events-modal');

    const modalContent = document.createElement('div');
    modalContent.classList.add('day-events-modal__content');

    // Create close button
    const closeButton = document.createElement('button');
    closeButton.classList.add('day-events-modal__close');
    closeButton.innerHTML = '&times;';
    closeButton.addEventListener('click', () => {
        document.body.removeChild(modal);
        removeOverlay(); // Xóa overlay khi đóng modal
    });

    // Create title
    const title = document.createElement('h2');
    title.classList.add('day-events-modal__title');
    title.textContent = `Công việc ngày ${formatDateForDisplay(date)}`;

    // Create events list
    const eventsList = document.createElement('div');
    eventsList.classList.add('day-events-modal__list');

    events.forEach(event => {
        const eventItem = document.createElement('div');
        eventItem.classList.add('day-events-modal__item');

        // Set left border color based on priority or event color
        let borderColor = '#3490dc'; // Default blue

        if (event.color) {
            borderColor = event.color;
        } else if (event.priority) {
            switch(event.priority) {
                case 'high':
                    borderColor = '#f56565'; // Red for high priority
                    break;
                case 'medium':
                    borderColor = '#ed8936'; // Orange for medium priority
                    break;
                case 'low':
                    borderColor = '#48bb78'; // Green for low priority
                    break;
            }
        }

        eventItem.style.borderLeftColor = borderColor;

        // Create event title
        const eventTitle = document.createElement('h3');
        eventTitle.classList.add('day-events-modal__item-title');
        eventTitle.textContent = event.title;

        // Create event details
        const eventDetails = document.createElement('div');
        eventDetails.classList.add('day-events-modal__item-details');

        // Add status if available
        if (event.status) {
            const statusElement = document.createElement('span');
            statusElement.classList.add('day-events-modal__item-status');
            statusElement.classList.add(`status-${event.status}`);
            statusElement.textContent = event.status === 'completed' ? 'Hoàn thành' :
                                        event.status === 'in_progress' ? 'Đang thực hiện' : 'Chờ xử lý';
            eventDetails.appendChild(statusElement);
        }

        // Add event item to list
        eventItem.appendChild(eventTitle);
        eventItem.appendChild(eventDetails);

        // Add click event to show full details
        eventItem.addEventListener('click', () => {
            document.body.removeChild(modal);
            showEventDetails(event);
        });

        eventsList.appendChild(eventItem);
    });

    // Assemble modal
    modalContent.appendChild(closeButton);
    modalContent.appendChild(title);
    modalContent.appendChild(eventsList);
    modal.appendChild(modalContent);

    // Add modal to body
    document.body.appendChild(modal);

    // Close modal when clicking outside
    modal.addEventListener('click', (e) => {
        if (e.target === modal) {
            document.body.removeChild(modal);
            removeOverlay(); // Xóa overlay khi đóng modal
        }
    });
}

// Setup event handlers
function setupEventHandlers() {
    // Previous button
    const prevButton = document.querySelector('[data-nav-previous-button]');
    if (prevButton) {
        prevButton.addEventListener('click', navigatePrevious);
    }

    // Next button
    const nextButton = document.querySelector('[data-nav-next-button]');
    if (nextButton) {
        nextButton.addEventListener('click', navigateNext);
    }

    // Today button
    const todayButton = document.querySelector('[data-nav-today-button]');
    if (todayButton) {
        todayButton.addEventListener('click', navigateToday);
    }

    // View select
    const viewSelect = document.querySelector('[data-view-select]');
    if (viewSelect) {
        viewSelect.addEventListener('change', changeView);
    }

    // Mini calendar previous button
    const miniPrevButton = document.querySelector('[data-mini-calendar-previous-button]');
    if (miniPrevButton) {
        miniPrevButton.addEventListener('click', navigateMiniPrevious);
    }

    // Mini calendar next button
    const miniNextButton = document.querySelector('[data-mini-calendar-next-button]');
    if (miniNextButton) {
        miniNextButton.addEventListener('click', navigateMiniNext);
    }

    // Create event button
    const createEventButtons = document.querySelectorAll('[data-event-create-button]');
    createEventButtons.forEach(button => {
        button.addEventListener('click', openCreateEventDialog);
    });
}

// Navigation functions
function navigatePrevious() {
    // Create a new date object to avoid mutating the original date
    const newDate = new Date(calendarState.currentDate);

    if (calendarState.currentView === 'month') {
        newDate.setDate(1); // Set to first day of month to avoid date overflow
        newDate.setMonth(newDate.getMonth() - 1);
    } else if (calendarState.currentView === 'week') {
        newDate.setDate(newDate.getDate() - 7);
    } else if (calendarState.currentView === 'day') {
        newDate.setDate(newDate.getDate() - 1);
    }

    calendarState.currentDate = newDate;
    updateDateDisplay();
    renderCalendar();

    // Không hiển thị thông báo khi điều hướng
}

function navigateNext() {
    // Create a new date object to avoid mutating the original date
    const newDate = new Date(calendarState.currentDate);

    if (calendarState.currentView === 'month') {
        newDate.setDate(1); // Set to first day of month to avoid date overflow
        newDate.setMonth(newDate.getMonth() + 1);
    } else if (calendarState.currentView === 'week') {
        newDate.setDate(newDate.getDate() + 7);
    } else if (calendarState.currentView === 'day') {
        newDate.setDate(newDate.getDate() + 1);
    }

    calendarState.currentDate = newDate;
    updateDateDisplay();
    renderCalendar();

    // Không hiển thị thông báo khi điều hướng
}

function navigateToday() {
    // Set current date to today
    calendarState.currentDate = new Date();

    // Update all displays
    updateDateDisplay();
    renderCalendar();

    // Highlight today in mini-calendar
    const allDays = document.querySelectorAll('.mini-calendar__day');
    allDays.forEach(day => {
        day.classList.remove('selected');
        if (day.classList.contains('today')) {
            day.classList.add('selected');
        }
    });

    // Không hiển thị thông báo khi chuyển đến ngày hôm nay
}

function changeView(event) {
    // Update the current view
    calendarState.currentView = event.target.value;

    // Log the view change (chỉ hiển thị trong console, không hiển thị cho người dùng)
    console.log('Chuyển đổi chế độ xem sang:', calendarState.currentView);

    // Update the date display
    updateDateDisplay();

    // Render the calendar with the new view
    renderCalendar();

    // Không hiển thị thông báo khi chuyển đổi chế độ xem
}

function navigateMiniPrevious() {
    // Create a new date object to avoid mutating the original date
    const newDate = new Date(calendarState.currentDate);
    newDate.setDate(1); // Set to first day of month to avoid date overflow
    newDate.setMonth(newDate.getMonth() - 1);
    calendarState.currentDate = newDate;

    updateDateDisplay();
    renderCalendar();
}

function navigateMiniNext() {
    // Create a new date object to avoid mutating the original date
    const newDate = new Date(calendarState.currentDate);
    newDate.setDate(1); // Set to first day of month to avoid date overflow
    newDate.setMonth(newDate.getMonth() + 1);
    calendarState.currentDate = newDate;

    updateDateDisplay();
    renderCalendar();
}

// Fetch events from API
function fetchEvents() {
    // Lấy CSRF token từ meta tag
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

    console.log('Đang tải sự kiện từ API...');

    // Không hiển thị thông báo đang tải

    // Gọi API để lấy sự kiện
    fetch('/api/calendar/events', {
        method: 'GET',
        headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': csrfToken || '',
            'Content-Type': 'application/json'
        },
        credentials: 'same-origin'
    })
    .then(response => {
        console.log('Phản hồi API:', response.status);

        if (response.ok) {
            return response.json();
        } else {
            if (response.status === 401) {
                // Lỗi xác thực - có thể token hết hạn
                console.error('Lỗi xác thực (401) - Token không hợp lệ hoặc hết hạn');
                throw new Error('Phiên đăng nhập hết hạn. Vui lòng đăng nhập lại.');
            } else {
                console.error('Lỗi API:', response.status);
                throw new Error('Không thể tải dữ liệu: ' + response.statusText);
            }
        }
    })
    .then(data => {
        console.log('Dữ liệu nhận được:', data);

        // Xử lý dữ liệu từ API
        if (Array.isArray(data)) {
            console.log('Nhận được mảng dữ liệu với', data.length, 'sự kiện');
            calendarState.events = data;

            // Không hiển thị thông báo thành công
        }
        // Nếu API trả về object có thuộc tính data hoặc events
        else if (data && (data.data || data.events)) {
            console.log('Dữ liệu có thuộc tính data/events');
            const events = data.data || data.events;
            calendarState.events = events;

            // Không hiển thị thông báo thành công
        }
        // Nếu không có dữ liệu hợp lệ
        else {
            console.log('Không có định dạng dữ liệu hợp lệ, sử dụng mảng rỗng');
            calendarState.events = [];
            // Không hiển thị thông báo
        }

        // Log số lượng sự kiện
        console.log('Tổng số sự kiện:', calendarState.events.length);

        // Nếu không có sự kiện nào, thêm sự kiện mẫu
        if (calendarState.events.length === 0) {
            console.log('Không có sự kiện, thêm sự kiện mẫu');
            const today = new Date();
            const formattedDate = formatDateForInput(today);

            calendarState.events = [
                {
                    id: 1,
                    title: 'Họp nhóm (mẫu)',
                    date: formattedDate,
                    startTime: 540, // 9:00 AM (in minutes from midnight)
                    endTime: 600, // 10:00 AM
                    color: '#2563eb',
                    priority: 'high',
                    status: 'pending'
                },
                {
                    id: 2,
                    title: 'Ăn trưa với khách hàng (mẫu)',
                    date: formattedDate,
                    startTime: 720, // 12:00 PM
                    endTime: 780, // 1:00 PM
                    color: '#f59e0b',
                    priority: 'medium',
                    status: 'pending'
                }
            ];

            // Không hiển thị thông báo dữ liệu mẫu
        }

        // Render calendar with events
        renderCalendar();
    })
    .catch(error => {
        console.error('Lỗi khi tải sự kiện:', error);

        // Chỉ hiển thị thông báo lỗi nghiêm trọng
        if (error.message.includes('đăng nhập')) {
            showNotification('Phiên đăng nhập hết hạn. Vui lòng đăng nhập lại.', 'error');
        }

        // Sử dụng dữ liệu mẫu nếu có lỗi
        const today = new Date();
        const formattedDate = formatDateForInput(today);

        calendarState.events = [
            {
                id: 1,
                title: 'Họp nhóm (mẫu - lỗi kết nối)',
                date: formattedDate,
                startTime: 540,
                endTime: 600,
                color: '#2563eb',
                priority: 'high',
                status: 'pending'
            },
            {
                id: 2,
                title: 'Ăn trưa với khách hàng (mẫu - lỗi kết nối)',
                date: formattedDate,
                startTime: 720,
                endTime: 780,
                color: '#f59e0b',
                priority: 'medium',
                status: 'pending'
            }
        ];

        // Render calendar with events
        renderCalendar();
    });
}

// Tạo và quản lý overlay
function createOverlay() {
    // Kiểm tra xem overlay đã tồn tại chưa
    let overlay = document.querySelector('.page-overlay');

    if (!overlay) {
        // Tạo overlay mới nếu chưa tồn tại
        overlay = document.createElement('div');
        overlay.classList.add('page-overlay');
        document.body.appendChild(overlay);
    }

    // Hiển thị overlay
    overlay.style.display = 'block';

    return overlay;
}

function removeOverlay() {
    const overlay = document.querySelector('.page-overlay');
    if (overlay) {
        overlay.style.display = 'none';
    }
}

// Open create event dialog
function openCreateEventDialog() {
    // Tạo overlay trước khi mở dialog
    createOverlay();

    const dialog = document.querySelector('[data-dialog="event-form"]');
    if (dialog) {
        // Set dialog title
        const dialogTitle = dialog.querySelector('[data-dialog-title]');
        if (dialogTitle) {
            dialogTitle.textContent = 'Tạo công việc mới';
        }

        // Thêm sự kiện để xóa overlay khi đóng dialog
        dialog.addEventListener('close', removeOverlay, { once: true });

        // Set default dates to current date
        const startDateInput = dialog.querySelector('#start_date');
        const dueDateInput = dialog.querySelector('#due_date');

        // Đảm bảo ngày được đặt đúng
        const currentDate = new Date(calendarState.currentDate);

        if (startDateInput) {
            // Đặt giá trị và thuộc tính value để đảm bảo cả hai đều được cập nhật
            const startDateValue = formatDateForInput(currentDate);
            startDateInput.value = startDateValue;
            startDateInput.setAttribute('value', startDateValue);
            console.log('Start date set to:', startDateValue);
        }

        if (dueDateInput) {
            // Mặc định ngày hết hạn là 1 tuần sau ngày bắt đầu
            const dueDate = new Date(currentDate);
            dueDate.setDate(dueDate.getDate() + 7);
            const dueDateValue = formatDateForInput(dueDate);
            dueDateInput.value = dueDateValue;
            dueDateInput.setAttribute('value', dueDateValue);
            console.log('Due date set to:', dueDateValue);
        }

        // Đảm bảo form có action đúng
        const form = dialog.querySelector('form');
        if (form) {
            // Đảm bảo form sử dụng phương thức POST
            form.method = 'POST';

            // Thêm xử lý submit form
            form.onsubmit = function(e) {
                e.preventDefault();

                // Lấy dữ liệu form và chuyển thành JSON
                const formData = new FormData(form);
                const formDataObj = {};

                // Chuyển FormData thành object
                for (let [key, value] of formData.entries()) {
                    formDataObj[key] = value;
                }

                // Đảm bảo các trường ngày được đặt đúng
                const startDateInput = form.querySelector('#start_date');
                const dueDateInput = form.querySelector('#due_date');

                if (startDateInput && startDateInput.value) {
                    formDataObj.start_date = startDateInput.value;
                }

                if (dueDateInput && dueDateInput.value) {
                    formDataObj.due_date = dueDateInput.value;
                }

                // Debug: Hiển thị dữ liệu form
                console.log('Form data:', formDataObj);

                // Thêm token JWT nếu có
                const token = localStorage.getItem('jwt_token');

                // Lấy CSRF token từ meta tag
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

                console.log('Submitting form to:', form.action);

                // Gửi request bằng fetch API - sử dụng API endpoint phù hợp
                fetch('/api/calendar/events', {
                    method: 'POST',
                    body: JSON.stringify(formDataObj),
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Authorization': token ? `Bearer ${token}` : '',
                        'X-CSRF-TOKEN': csrfToken || '',
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    credentials: 'same-origin'
                })
                .then(response => {
                    // Nếu response không ok, xử lý lỗi
                    if (!response.ok) {
                        // Tạo một đối tượng lỗi với response để xử lý sau
                        const error = new Error('Lỗi khi tạo công việc: ' + response.status);
                        error.response = response;
                        throw error;
                    }

                    // Kiểm tra content-type
                    const contentType = response.headers.get('content-type');
                    if (contentType && contentType.includes('application/json')) {
                        return response.json();
                    } else {
                        // Nếu không phải JSON, trả về object đơn giản
                        return { success: true, message: 'Công việc đã được tạo thành công!' };
                    }
                })
                .then(data => {
                    console.log('Công việc đã được tạo:', data);

                    // Hiển thị thông báo thành công
                    showNotification('Công việc đã được tạo thành công!', 'success');

                    // Reset form
                    form.reset();

                    // Đóng dialog sau một khoảng thời gian ngắn
                    setTimeout(() => {
                        dialog.close();

                        // Cập nhật lại calendar
                        fetchEvents();
                    }, 500);
                })
                .catch(error => {
                    console.error('Lỗi khi tạo công việc:', error);

                    // Xử lý phản hồi lỗi từ server
                    if (error.response) {
                        // Lỗi validation
                        error.response.json().then(data => {
                            let errorMessage = 'Lỗi khi tạo công việc: ';
                            if (data.errors) {
                                // Lấy tất cả các lỗi validation
                                const errorMessages = Object.values(data.errors).flat();
                                errorMessage += errorMessages.join(', ');
                            } else if (data.message) {
                                errorMessage += data.message;
                            } else {
                                errorMessage += 'Dữ liệu không hợp lệ';
                            }
                            showNotification(errorMessage, 'error');
                        }).catch(jsonError => {
                            console.error('Lỗi khi parse JSON:', jsonError);
                            showNotification('Lỗi khi tạo công việc: Dữ liệu không hợp lệ', 'error');
                        });
                    } else {
                        // Hiển thị thông báo lỗi chung
                        showNotification('Lỗi khi tạo công việc: ' + error.message, 'error');
                    }
                });
            };
        }

        // Xử lý nút đóng dialog
        const closeButtons = dialog.querySelectorAll('[data-dialog-close-button]');
        closeButtons.forEach(button => {
            // Xóa tất cả event listeners cũ
            const newButton = button.cloneNode(true);
            button.parentNode.replaceChild(newButton, button);

            // Thêm event listener mới
            newButton.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                dialog.close();
            });
        });

        // Show dialog
        dialog.showModal();

        // Thêm sự kiện click bên ngoài để đóng dialog
        dialog.addEventListener('click', function(e) {
            if (e.target === dialog) {
                dialog.close();
            }
        });
    }
}

// Hiển thị thông báo
function showNotification(message, type = 'info') {
    // Tạo phần tử thông báo
    const notification = document.createElement('div');
    notification.className = 'notification';
    notification.classList.add(`notification--${type}`);

    // Thêm nội dung
    notification.textContent = message;

    // Thêm vào body
    document.body.appendChild(notification);

    // Hiển thị thông báo
    setTimeout(() => {
        notification.classList.add('notification--visible');
    }, 10);

    // Tự động ẩn sau 3 giây
    setTimeout(() => {
        notification.classList.remove('notification--visible');

        // Xóa khỏi DOM sau khi animation kết thúc
        setTimeout(() => {
            notification.remove();
        }, 300);
    }, 3000);
}

// Format date for input (YYYY-MM-DD)
function formatDateForInput(date) {
    const year = date.getFullYear();
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const day = String(date.getDate()).padStart(2, '0');
    return `${year}-${month}-${day}`;
}

// Render week view
function renderWeekView(calendarElement) {
    console.log('Rendering week view');

    // Create week view container
    const weekView = document.createElement('div');
    weekView.classList.add('week-view');

    // Get start of week (Sunday)
    const startOfWeek = getStartOfWeek(calendarState.currentDate);

    // Create time column
    const timeColumn = document.createElement('div');
    timeColumn.classList.add('week-view__time-column');

    // Add header to time column
    const timeHeader = document.createElement('div');
    timeHeader.classList.add('week-view__header', 'week-view__header--time');
    timeColumn.appendChild(timeHeader);

    // Add time slots
    for (let hour = 0; hour < 24; hour++) {
        const timeSlot = document.createElement('div');
        timeSlot.classList.add('week-view__time-slot');
        timeSlot.textContent = `${hour}:00`;
        timeColumn.appendChild(timeSlot);
    }

    weekView.appendChild(timeColumn);

    // Create day columns
    for (let i = 0; i < 7; i++) {
        const day = new Date(startOfWeek);
        day.setDate(day.getDate() + i);

        const dayColumn = document.createElement('div');
        dayColumn.classList.add('week-view__day-column');
        dayColumn.dataset.dayIndex = i; // Thêm thuộc tính data-day-index

        // Add header with day name
        const dayHeader = document.createElement('div');
        dayHeader.classList.add('week-view__header');

        const dayName = day.toLocaleDateString('vi-VN', { weekday: 'short' });
        const dayNumber = day.getDate();
        dayHeader.innerHTML = `<div>${dayName}</div><div>${dayNumber}</div>`;

        if (isDateToday(day)) {
            dayHeader.classList.add('week-view__header--today');
        }

        dayColumn.appendChild(dayHeader);

        // Add day content container
        const dayContent = document.createElement('div');
        dayContent.classList.add('week-view__day-content');

        // Add time slots
        for (let hour = 0; hour < 24; hour++) {
            const timeSlot = document.createElement('div');
            timeSlot.classList.add('week-view__time-slot');

            // Store date and time in data attributes
            const dateStr = formatDateForInput(day);
            timeSlot.dataset.date = dateStr;
            timeSlot.dataset.hour = hour;

            // Add click event to create event at this time
            timeSlot.addEventListener('click', () => {
                const newDate = new Date(day);
                newDate.setHours(hour, 0, 0, 0);
                calendarState.currentDate = newDate;
                openCreateEventDialog();
            });

            dayContent.appendChild(timeSlot);
        }

        dayColumn.appendChild(dayContent);
        weekView.appendChild(dayColumn);
    }

    calendarElement.appendChild(weekView);

    // Debug
    console.log('Calendar events before adding to week view:', calendarState.events);

    // Add events to week view
    addEventsToWeekView();
}

// Add events to week view
function addEventsToWeekView() {
    // Kiểm tra xem có sự kiện nào không
    if (!calendarState.events || calendarState.events.length === 0) {
        console.log('Không có sự kiện để hiển thị');
        return;
    }

    console.log('Thêm sự kiện vào chế độ xem tuần, tổng số:', calendarState.events.length);

    // Lấy ngày bắt đầu tuần
    const startOfWeek = getStartOfWeek(calendarState.currentDate);
    const endOfWeek = new Date(startOfWeek);
    endOfWeek.setDate(endOfWeek.getDate() + 6);

    // Log để debug
    console.log('Ngày bắt đầu tuần:', startOfWeek.toISOString().split('T')[0]);
    console.log('Ngày kết thúc tuần:', endOfWeek.toISOString().split('T')[0]);

    // Lọc các sự kiện trong tuần này - ưu tiên sử dụng start_date
    const weekEvents = calendarState.events.filter(event => {
        // Xử lý các định dạng ngày khác nhau - ưu tiên start_date
        let eventDate;
        if (event.start_date) {
            eventDate = new Date(event.start_date);
        } else if (event.date) {
            eventDate = new Date(event.date);
        } else if (event.due_date) {
            eventDate = new Date(event.due_date);
        } else if (event.start) {
            eventDate = new Date(event.start.split('T')[0]);
        } else {
            console.log('Sự kiện không có ngày:', event);
            return false;
        }

        // Log để debug
        console.log('Sự kiện:', event.title, 'ngày:', eventDate.toISOString().split('T')[0]);

        // Chuyển đổi thành chuỗi ngày để so sánh chính xác
        const eventDateStr = eventDate.toISOString().split('T')[0];
        const startOfWeekStr = startOfWeek.toISOString().split('T')[0];
        const endOfWeekStr = endOfWeek.toISOString().split('T')[0];

        // Kiểm tra nếu ngày sự kiện nằm trong khoảng từ startOfWeek đến endOfWeek
        return eventDateStr >= startOfWeekStr && eventDateStr <= endOfWeekStr;
    });

    console.log('Sự kiện trong tuần này:', weekEvents.length);

    // Thêm sự kiện vào các cột ngày
    weekEvents.forEach(event => {
        try {
            // Lấy ngày của sự kiện
            let eventDate;
            if (event.date) {
                eventDate = new Date(event.date);
            } else if (event.due_date) {
                eventDate = new Date(event.due_date);
            } else if (event.start_date) {
                eventDate = new Date(event.start_date);
            } else if (event.start) {
                eventDate = new Date(event.start.split('T')[0]);
            } else {
                return; // Bỏ qua sự kiện không có ngày
            }

            // Tính chỉ số ngày (0-6)
            const dayDiff = Math.floor((eventDate - startOfWeek) / (24 * 60 * 60 * 1000));

            // Đảm bảo chỉ số ngày nằm trong khoảng 0-6
            const dayIndex = Math.max(0, Math.min(6, dayDiff));

            console.log('Sự kiện:', event.title, 'chỉ số ngày:', dayIndex);

            // Tìm cột ngày theo chỉ số
            const dayColumn = document.querySelector(`.week-view__day-column[data-day-index="${dayIndex}"]`);

            if (!dayColumn) {
                console.error('Không tìm thấy cột ngày cho chỉ số:', dayIndex);
                return;
            }

            const dayContent = dayColumn.querySelector('.week-view__day-content');

            // Tạo phần tử sự kiện
            const eventElement = document.createElement('div');
            eventElement.classList.add('week-view__event');
            eventElement.textContent = event.title || event.name || 'Sự kiện không tên';

            // Đặt vị trí và chiều cao
            let startTime = 9 * 60; // Mặc định 9:00 AM (in minutes)
            let duration = 60; // Mặc định 1 giờ (in minutes)

            // Sử dụng startTime từ sự kiện nếu có
            if (event.startTime !== undefined) {
                startTime = event.startTime;
            }

            // Tính toán thời lượng nếu có endTime
            if (event.endTime !== undefined) {
                duration = event.endTime - startTime;
            }

            // Đặt vị trí và kích thước
            eventElement.style.top = `${startTime}px`;
            eventElement.style.height = `${duration}px`;

            // Đặt màu sắc
            let bgColor = '#3490dc'; // Màu xanh mặc định

            if (event.color) {
                bgColor = event.color;
            } else if (event.priority) {
                switch(event.priority) {
                    case 'high':
                        bgColor = '#f56565'; // Đỏ cho ưu tiên cao
                        break;
                    case 'medium':
                        bgColor = '#ed8936'; // Cam cho ưu tiên trung bình
                        break;
                    case 'low':
                        bgColor = '#48bb78'; // Xanh lá cho ưu tiên thấp
                        break;
                }
            }

            eventElement.style.backgroundColor = bgColor;

            // Thêm sự kiện click để hiển thị chi tiết
            eventElement.addEventListener('click', (e) => {
                e.stopPropagation();
                showEventDetails(event);
            });

            // Thêm sự kiện vào nội dung ngày
            dayContent.appendChild(eventElement);
            console.log('Đã thêm sự kiện:', event.title || event.name);
        } catch (error) {
            console.error('Lỗi khi thêm sự kiện:', error);
        }
    });
}

// Render day view
function renderDayView(calendarElement) {
    console.log('Đang hiển thị chế độ xem ngày');

    // Create day view container
    const dayView = document.createElement('div');
    dayView.classList.add('day-view');

    // Create time column
    const timeColumn = document.createElement('div');
    timeColumn.classList.add('day-view__time-column');

    // Add header to time column
    const timeHeader = document.createElement('div');
    timeHeader.classList.add('day-view__header', 'day-view__header--time');
    timeColumn.appendChild(timeHeader);

    // Add time slots
    for (let hour = 0; hour < 24; hour++) {
        const timeSlot = document.createElement('div');
        timeSlot.classList.add('day-view__time-slot');
        timeSlot.textContent = `${hour}:00`;
        timeColumn.appendChild(timeSlot);
    }

    dayView.appendChild(timeColumn);

    // Create day column
    const dayColumn = document.createElement('div');
    dayColumn.classList.add('day-view__day-column');
    dayColumn.dataset.date = formatDateForInput(calendarState.currentDate);

    // Add header with day name
    const dayHeader = document.createElement('div');
    dayHeader.classList.add('day-view__header');

    const day = calendarState.currentDate;
    const dayName = day.toLocaleDateString('vi-VN', { weekday: 'long' });
    const dayNumber = day.getDate();
    const monthName = day.toLocaleDateString('vi-VN', { month: 'long' });
    dayHeader.innerHTML = `<div>${dayName}</div><div>${dayNumber} ${monthName}</div>`;

    if (isDateToday(day)) {
        dayHeader.classList.add('day-view__header--today');
    }

    dayColumn.appendChild(dayHeader);

    // Add day content container
    const dayContent = document.createElement('div');
    dayContent.classList.add('day-view__day-content');

    // Add time slots
    for (let hour = 0; hour < 24; hour++) {
        const timeSlot = document.createElement('div');
        timeSlot.classList.add('day-view__time-slot');

        // Store date and time in data attributes
        const dateStr = formatDateForInput(day);
        timeSlot.dataset.date = dateStr;
        timeSlot.dataset.hour = hour;

        // Add click event to create event at this time
        timeSlot.addEventListener('click', () => {
            const newDate = new Date(day);
            newDate.setHours(hour, 0, 0, 0);
            calendarState.currentDate = newDate;
            openCreateEventDialog();
        });

        dayContent.appendChild(timeSlot);
    }

    dayColumn.appendChild(dayContent);
    dayView.appendChild(dayColumn);

    calendarElement.appendChild(dayView);

    // Debug
    console.log('Sự kiện trước khi thêm vào chế độ xem ngày:', calendarState.events);

    // Add events to day view
    addEventsToDayView();
}

// Add events to day view
function addEventsToDayView() {
    if (!calendarState.events || calendarState.events.length === 0) {
        console.log('Không có sự kiện để hiển thị trong chế độ xem ngày');
        return;
    }

    console.log('Thêm sự kiện vào chế độ xem ngày, tổng số:', calendarState.events.length);

    // Get current day
    const currentDay = new Date(calendarState.currentDate);
    currentDay.setHours(0, 0, 0, 0);

    // Format current day for comparison
    const currentDayStr = currentDay.toISOString().split('T')[0];
    console.log('Ngày hiện tại:', currentDayStr);

    // In tất cả các sự kiện để debug
    console.log('Tất cả sự kiện:', calendarState.events);

    // Filter events for this day - ưu tiên sử dụng start_date
    const dayEvents = calendarState.events.filter(event => {
        // Handle different date formats - ưu tiên start_date
        let eventDate;
        let eventDateStr;

        if (event.start_date) {
            eventDate = new Date(event.start_date);
            eventDateStr = event.start_date;
        } else if (event.date) {
            eventDate = new Date(event.date);
            eventDateStr = event.date;
        } else if (event.due_date) {
            eventDate = new Date(event.due_date);
            eventDateStr = event.due_date;
        } else if (event.start) {
            eventDate = new Date(event.start.split('T')[0]);
            eventDateStr = event.start.split('T')[0];
        } else {
            console.log('Sự kiện không có ngày:', event);
            return false; // Skip events without date
        }

        // Debug
        console.log('Sự kiện:', event.title, 'ngày:', eventDateStr);

        // Compare date strings for exact match
        if (typeof eventDateStr === 'string') {
            const eventDateOnly = eventDateStr.split('T')[0];
            console.log('So sánh:', eventDateOnly, '===', currentDayStr);
            return eventDateOnly === currentDayStr;
        }

        // Fallback to date object comparison
        eventDate.setHours(0, 0, 0, 0);
        return eventDate.getTime() === currentDay.getTime();
    });

    console.log('Sự kiện trong ngày này:', dayEvents.length);

    // Add events to day column
    dayEvents.forEach(event => {
        try {
            // Get start and end time
            let startTime = 9 * 60; // Default to 9:00 AM (in minutes)
            let duration = 60; // Default to 1 hour (in minutes)

            // Use startTime from event if available
            if (event.startTime !== undefined) {
                startTime = event.startTime;
            } else if (event.start_time !== undefined) {
                startTime = event.start_time;
            }

            // Calculate duration if endTime is available
            if (event.endTime !== undefined) {
                duration = event.endTime - startTime;
            } else if (event.end_time !== undefined) {
                duration = event.end_time - startTime;
            }

            // If no time specified, use default times based on priority
            if (event.priority && (event.startTime === undefined && event.start_time === undefined)) {
                switch(event.priority) {
                    case 'high':
                        startTime = 8 * 60; // 8:00 AM
                        duration = 120; // 2 hours
                        break;
                    case 'medium':
                        startTime = 12 * 60; // 12:00 PM
                        duration = 120; // 2 hours
                        break;
                    case 'low':
                        startTime = 15 * 60; // 3:00 PM
                        duration = 60; // 1 hour
                        break;
                }
            }

            // Find day column
            const dayColumn = document.querySelector('.day-view__day-column');
            if (!dayColumn) {
                console.error('Không tìm thấy cột ngày');
                return;
            }

            const dayContent = dayColumn.querySelector('.day-view__day-content');
            if (!dayContent) {
                console.error('Không tìm thấy nội dung ngày');
                return;
            }

            // Create event element
            const eventElement = document.createElement('div');
            eventElement.classList.add('day-view__event');
            eventElement.textContent = event.title || 'Sự kiện không tên';

            // Set position and height
            eventElement.style.top = `${startTime}px`;
            eventElement.style.height = `${duration}px`;

            // Set background color based on priority or event color
            let bgColor = '#3490dc'; // Default blue

            if (event.color) {
                bgColor = event.color;
            } else if (event.priority) {
                switch(event.priority) {
                    case 'high':
                        bgColor = '#f56565'; // Red for high priority
                        break;
                    case 'medium':
                        bgColor = '#ed8936'; // Orange for medium priority
                        break;
                    case 'low':
                        bgColor = '#48bb78'; // Green for low priority
                        break;
                }
            }

            eventElement.style.backgroundColor = bgColor;

            // Add click event to show event details
            eventElement.addEventListener('click', (e) => {
                e.stopPropagation(); // Prevent time slot click event
                showEventDetails(event);
            });

            // Add event to day content
            dayContent.appendChild(eventElement);
            console.log('Đã thêm sự kiện:', event.title);
        } catch (error) {
            console.error('Lỗi khi thêm sự kiện vào chế độ xem ngày:', error);
        }
    });

    // Hiển thị thông báo
    if (dayEvents.length > 0) {
        showNotification(`Đã hiển thị ${dayEvents.length} sự kiện cho ngày này`, 'success');
    } else {
        showNotification('Không có sự kiện nào cho ngày này', 'info');
    }
}

// Hàm tải các sự kiện từ API
function loadEvents() {
    console.log('Đang tải sự kiện...');

    // Gọi API để lấy danh sách công việc
    fetch('/api/tasks', {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        },
        credentials: 'same-origin'
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`Lỗi API: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        console.log('Dữ liệu nhận được:', data);

        // Lưu sự kiện vào state
        calendarState.events = Array.isArray(data) ? data : (data.data || []);

        // Hiển thị số lượng sự kiện đã tải
        console.log(`Đã tải ${calendarState.events.length} sự kiện`);

        // Vẽ lại lịch với các sự kiện
        renderCalendar();
    })
    .catch(error => {
        console.error('Lỗi khi tải sự kiện:', error);
    });
}

// Hàm gỡ lỗi để kiểm tra kết nối API
function kiemTraAPI() {
    console.log('Đang kiểm tra API...');

    // Hiển thị thông tin gỡ lỗi
    const thongTinLoi = document.createElement('div');
    thongTinLoi.style.position = 'fixed';
    thongTinLoi.style.bottom = '10px';
    thongTinLoi.style.right = '10px';
    thongTinLoi.style.backgroundColor = 'rgba(0,0,0,0.8)';
    thongTinLoi.style.color = 'white';
    thongTinLoi.style.padding = '10px';
    thongTinLoi.style.borderRadius = '5px';
    thongTinLoi.style.zIndex = '9999';
    thongTinLoi.style.maxWidth = '300px';
    thongTinLoi.style.maxHeight = '200px';
    thongTinLoi.style.overflow = 'auto';
    thongTinLoi.textContent = 'Đang kiểm tra kết nối API...';
    document.body.appendChild(thongTinLoi);

    // Gọi API để lấy danh sách công việc
    fetch('/api/tasks', {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        },
        credentials: 'same-origin'
    })
    .then(response => {
        thongTinLoi.textContent += `\nPhản hồi API: ${response.status}`;
        return response.json();
    })
    .then(data => {
        const events = Array.isArray(data) ? data : (data.data || []);
        thongTinLoi.textContent += `\nSố sự kiện tìm thấy: ${events.length}`;

        if (events.length > 0) {
            thongTinLoi.textContent += `\nSự kiện đầu tiên: ${JSON.stringify(events[0]).substring(0, 100)}...`;
        } else {
            thongTinLoi.textContent += '\nKhông tìm thấy sự kiện nào. Hãy thử tạo công việc trước.';
        }
    })
    .catch(error => {
        thongTinLoi.textContent += `\nLỗi: ${error.message}`;
    });

    // Thêm nút đóng
    const nutDong = document.createElement('button');
    nutDong.textContent = 'Đóng';
    nutDong.style.marginTop = '10px';
    nutDong.style.padding = '5px';
    nutDong.addEventListener('click', () => {
        document.body.removeChild(thongTinLoi);
    });
    thongTinLoi.appendChild(nutDong);
}



