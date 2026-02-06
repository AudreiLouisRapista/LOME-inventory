  
                                    // Basic calendar logic with sample events. No date libraries required.
                                    (function() {
                                        const daysGrid = document.getElementById('daysGrid');
                                        const monthLabel = document.getElementById('monthLabel');
                                        const prev = document.getElementById('prev');
                                        const next = document.getElementById('next');
                                        const todayBtn = document.getElementById('todayBtn');
                                        const quickAdd = document.getElementById('quickAdd');
                                        const eventsList = document.getElementById('eventsList');

                                        // Example events (date format: YYYY-MM-DD)
                                        let events = [{
                                                id: 1,
                                                date: '2025-04-09',
                                                title: 'Math Exam',
                                                type: 'work',
                                                time: '9:00 AM'
                                            },
                                            {
                                                id: 2,
                                                date: '2025-04-22',
                                                title: 'Field Trip',
                                                type: 'personal',
                                                time: '7:30 AM'
                                            },
                                            {
                                                id: 3,
                                                date: '2025-04-23',
                                                title: 'Project Deadline',
                                                type: 'urgent',
                                                time: '11:59 PM'
                                            },
                                            {
                                                id: 4,
                                                date: '2025-04-20',
                                                title: 'Parent Meeting',
                                                type: 'personal',
                                                time: '3:00 PM'
                                            },
                                        ];

                                        // default shown month
                                        let view = new Date();
                                        let selectedDate = formatDate(view);

                                        function formatDate(d) {
                                            const y = d.getFullYear(),
                                                m = (d.getMonth() + 1).toString().padStart(2, '0'),
                                                day = d.getDate().toString().padStart(2, '0');
                                            return `${y}-${m}-${day}`;
                                        }

                                        function render() {
                                            daysGrid.innerHTML = '';
                                            const year = view.getFullYear();
                                            const month = view.getMonth();

                                            monthLabel.textContent = view.toLocaleString(undefined, {
                                                month: 'long',
                                                year: 'numeric'
                                            });

                                            // first day of month (0=Sun)
                                            const first = new Date(year, month, 1);
                                            const startDay = first.getDay();

                                            // days in this month
                                            const daysInMonth = new Date(year, month + 1, 0).getDate();
                                            // days in previous month (for inactive fillers)
                                            const prevDays = new Date(year, month, 0).getDate();

                                            // show 6 rows (42 cells) for consistent grid
                                            const totalCells = 42;
                                            for (let i = 0; i < totalCells; i++) {
                                                const cell = document.createElement('div');
                                                cell.className = 'day';
                                                cell.setAttribute('role', 'gridcell');

                                                // calculate day number and which month it belongs to
                                                const dayIndex = i - startDay + 1;
                                                let cellDate, isActive = true;
                                                if (dayIndex <= 0) {
                                                    // previous month
                                                    const dayNum = prevDays + dayIndex;
                                                    cell.textContent = dayNum;
                                                    cell.classList.add('inactive');
                                                    isActive = false;
                                                    const prevDate = new Date(year, month - 1, dayNum);
                                                    cellDate = formatDate(prevDate);
                                                } else if (dayIndex > daysInMonth) {
                                                    // next month
                                                    const dayNum = dayIndex - daysInMonth;
                                                    cell.textContent = dayNum;
                                                    cell.classList.add('inactive');
                                                    isActive = false;
                                                    const nextDate = new Date(year, month + 1, dayNum);
                                                    cellDate = formatDate(nextDate);
                                                } else {
                                                    // this month
                                                    cell.textContent = dayIndex;
                                                    const d = new Date(year, month, dayIndex);
                                                    cellDate = formatDate(d);

                                                    // today
                                                    const now = new Date();
                                                    if (now.getFullYear() === d.getFullYear() && now.getMonth() === d.getMonth() && now
                                                        .getDate() === d.getDate()) {
                                                        cell.classList.add('today');
                                                    }
                                                }

                                                // mark if events exist on this date (show a color dot)
                                                const evs = events.filter(e => e.date === cellDate);
                                                if (evs.length) {
                                                    const dot = document.createElement('span');
                                                    dot.className = 'dot';
                                                    // pick color by priority: urgent>work>personal
                                                    const types = evs.map(x => x.type);
                                                    let color = '#888';
                                                    if (types.includes('urgent')) color = getComputedStyle(document.documentElement)
                                                        .getPropertyValue('--dot-urgent').trim();
                                                    else if (types.includes('work')) color = getComputedStyle(document.documentElement)
                                                        .getPropertyValue('--dot-work').trim();
                                                    else if (types.includes('personal')) color = getComputedStyle(document.documentElement)
                                                        .getPropertyValue('--dot-personal').trim();
                                                    dot.style.background = color;
                                                    cell.appendChild(dot);
                                                }

                                                // selected state
                                                if (cellDate === selectedDate) {
                                                    cell.classList.add('selected');
                                                }

                                                // click to select
                                                cell.addEventListener('click', () => {
                                                    selectedDate = cellDate;
                                                    render();
                                                    // scroll focus for accessibility
                                                    cell.focus && cell.focus();
                                                });

                                                daysGrid.appendChild(cell);
                                            }

                                            renderEventsList();
                                        }

                                        function renderEventsList() {
                                            // show events for selected date and sample of that month
                                            eventsList.innerHTML = '';
                                            const dateEvents = events.filter(e => e.date === selectedDate);
                                            const selectedDateObj = new Date(selectedDate);

                                            const header = document.createElement('div');
                                            header.className = 'event-row';
                                            header.innerHTML =
                                                `<div style="font-weight:700">${selectedDateObj.toLocaleDateString(undefined,{weekday:'long', month:'short', day:'numeric'})}</div><div style="color:var(--muted)">${dateEvents.length} event${dateEvents.length!==1?'s':''}</div>`;
                                            eventsList.appendChild(header);

                                            if (dateEvents.length === 0) {
                                                const empty = document.createElement('div');
                                                empty.style.color = 'var(--muted)';
                                                empty.style.fontSize = '13px';
                                                empty.style.paddingTop = '8px';
                                                empty.textContent = 'No events. Click + Add to create one.';
                                                eventsList.appendChild(empty);
                                            } else {
                                                dateEvents.forEach(ev => {
                                                    const row = document.createElement('div');
                                                    row.className = 'event-row';
                                                    const left = document.createElement('div');
                                                    left.className = 'event-left';
                                                    const dot = document.createElement('div');
                                                    dot.style.width = '12px';
                                                    dot.style.height = '12px';
                                                    dot.style.borderRadius = '50%';
                                                    dot.style.background = ev.type === 'urgent' ? getComputedStyle(document.documentElement)
                                                        .getPropertyValue('--dot-urgent').trim() :
                                                        ev.type === 'work' ? getComputedStyle(document.documentElement).getPropertyValue(
                                                            '--dot-work').trim() :
                                                        getComputedStyle(document.documentElement).getPropertyValue('--dot-personal')
                                                        .trim();
                                                    left.appendChild(dot);
                                                    const info = document.createElement('div');
                                                    info.innerHTML =
                                                        `<div class="event-title">${ev.title}</div><div class="event-meta">${ev.time}</div>`;
                                                    left.appendChild(info);

                                                    const right = document.createElement('div');
                                                    right.style.fontSize = '13px';
                                                    right.style.color = 'var(--muted)';
                                                    right.textContent = ev.type.charAt(0).toUpperCase() + ev.type.slice(1);

                                                    row.appendChild(left);
                                                    row.appendChild(right);
                                                    eventsList.appendChild(row);
                                                });
                                            }

                                            // Also show a couple upcoming in month (optional)
                                            const month = view.getMonth(),
                                                year = view.getFullYear();
                                            const monthEvents = events.filter(e => {
                                                const d = new Date(e.date);
                                                return d.getFullYear() === year && d.getMonth() === month && e.date !== selectedDate;
                                            }).slice(0, 3);

                                            if (monthEvents.length) {
                                                const hr = document.createElement('div');
                                                hr.style.borderTop = '1px dashed #f0f2f6';
                                                hr.style.marginTop = '8px';
                                                hr.style.paddingTop = '8px';
                                                eventsList.appendChild(hr);

                                                monthEvents.forEach(ev => {
                                                    const r = document.createElement('div');
                                                    r.className = 'event-row';
                                                    r.innerHTML =
                                                        `<div style="display:flex;gap:8px;align-items:center"><div style="width:10px;height:10px;border-radius:50%;background:${ev.type==='urgent'?getComputedStyle(document.documentElement).getPropertyValue('--dot-urgent').trim():ev.type==='work'?getComputedStyle(document.documentElement).getPropertyValue('--dot-work').trim():getComputedStyle(document.documentElement).getPropertyValue('--dot-personal').trim()}"></div><div style="font-weight:600">${ev.title}</div></div><div style="color:var(--muted)">${new Date(ev.date).getDate()}</div>`;
                                                    eventsList.appendChild(r);
                                                });
                                            }
                                        }

                                        prev.addEventListener('click', () => {
                                            view = new Date(view.getFullYear(), view.getMonth() - 1, 1);
                                            render();
                                        });

                                        next.addEventListener('click', () => {
                                            view = new Date(view.getFullYear(), view.getMonth() + 1, 1);
                                            render();
                                        });

                                        todayBtn.addEventListener('click', () => {
                                            view = new Date();
                                            selectedDate = formatDate(new Date());
                                            render();
                                        });

                                        quickAdd.addEventListener('click', () => {
                                            // quick add: ask a few prompts — simplified for demo
                                            const title = prompt('Event title (e.g. Parent Meeting):');
                                            if (!title) return;
                                            let date = prompt('Date (YYYY-MM-DD)', selectedDate);
                                            if (!date) return;
                                            const time = prompt('Time (e.g. 3:00 PM)', '9:00 AM') || '';
                                            const type = prompt('Type (work/personal/urgent)', 'work') || 'work';
                                            const id = Date.now();
                                            events.push({
                                                id,
                                                date,
                                                title,
                                                type: type.toLowerCase().trim(),
                                                time
                                            });
                                            // if user added event in different month, move view to that month
                                            const parts = date.split('-').map(Number);
                                            if (parts.length === 3) {
                                                view = new Date(parts[0], parts[1] - 1, 1);
                                                selectedDate = date;
                                            }
                                            render();
                                        });

                                        // initialize to today's month
                                        render();
                                    })();
                               