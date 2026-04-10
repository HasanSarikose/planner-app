const currentDate = new Date();
let currentMonth = currentDate.getMonth();
let currentYear = currentDate.getFullYear();

function changeMonth(dir) {
    currentMonth += dir;
    if (currentMonth > 11) { currentMonth = 0; currentYear++; }
    if (currentMonth < 0)  { currentMonth = 11; currentYear--; }
    renderCalendar(currentMonth, currentYear);
}

function renderCalendar(month, year) {
    const grid = document.getElementById('calendarGrid');
    const months = ["Ocak","Şubat","Mart","Nisan","Mayıs","Haziran",
        "Temmuz","Ağustos","Eylül","Ekim","Kasım","Aralık"];

    document.getElementById('monthYearDisplay').innerText = `${months[month]} ${year}`;
    grid.innerHTML = '';

    let firstDay = new Date(year, month, 1).getDay();
    firstDay = firstDay === 0 ? 6 : firstDay - 1;
    const daysInMonth = new Date(year, month + 1, 0).getDate();

    // Boş hücreler
    for (let i = 0; i < firstDay; i++) {
        const el = document.createElement('div');
        el.classList.add('calendar-cell', 'empty');
        grid.appendChild(el);
    }

    // Gün hücreleri
    for (let day = 1; day <= daysInMonth; day++) {
        const cell = document.createElement('div');
        cell.classList.add('calendar-cell');

        const dateStr = `${year}-${String(month+1).padStart(2,'0')}-${String(day).padStart(2,'0')}`;
        const cellDate = new Date(dateStr);

        const isToday = day === currentDate.getDate()
            && month === currentDate.getMonth()
            && year === currentDate.getFullYear();

        if (isToday) cell.classList.add('today');

        const num = document.createElement('div');
        num.classList.add('date-number');
        num.innerText = day;
        cell.appendChild(num);

        // Göreve denk gelen task tagları
        window.tasks
            .filter(t => {
                const s = new Date(t.startDate);
                const e = new Date(t.endDate);
                return cellDate >= s && cellDate <= e;
            })
            .forEach(task => {
                const tag = document.createElement('div');
                tag.classList.add('task-tag');
                tag.innerText = task.title;
                tag.style.backgroundColor = task.color;
                tag.onclick = () => window.editTask(task.id);
                cell.appendChild(tag);
            });

        grid.appendChild(cell);
    }
}
