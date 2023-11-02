<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="light ">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap">
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Cairo&display=swap" rel="stylesheet">

        @livewireStyles

        <!-- Scripts -->
        @vite(['resources/sass/app.scss', 'resources/js/app.js'])
        <script>
            if (localStorage.getItem('dark-mode') === 'false' || !('dark-mode' in localStorage)) {
                document.querySelector('html').classList.remove('dark');
                document.querySelector('html').style.colorScheme = 'light';
            } else {
                document.querySelector('html').classList.add('dark');
                document.querySelector('html').style.colorScheme = 'dark';
            }
        </script>
    </head>
    <body  dir="{{ $localeDirs[App::getLocale()] }}"
        class="font-inter antialiased bg-slate-100 dark:bg-slate-900 text-slate-600 dark:text-slate-400  "
        :class="{ 'sidebar-expanded': sidebarExpanded }"
        x-data="{ sidebarOpen: false, sidebarExpanded: localStorage.getItem('sidebar-expanded') == 'true' ,darkMode: false }"
        x-init="$watch('sidebarExpanded', value => localStorage.setItem('sidebar-expanded', value));
    if (!('darkMode' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches) {
      localStorage.setItem('darkMode', JSON.stringify(true));
    }
    darkMode = JSON.parse(localStorage.getItem('darkMode'));
    $watch('darkMode', value => localStorage.setItem('darkMode', JSON.stringify(value)))" x-cloak>

        <!-- Page wrapper -->
        <div class="flex h-screen overflow-hidden   " id="app">

            <x-app.sidebar />

            <!-- Content area -->
            <div class="relative flex flex-col flex-1 overflow-y-auto overflow-x-hidden scrollbar-thin scrollbar-thumb-indigo-600 dark:scrollbar-thumb-indigo-700 scrollbar-track-gray-300 dark:scrollbar-track-gray-600 scrollbar-corner-blue-500 @if($attributes['background']){{ $attributes['background'] }}@endif" x-ref="contentarea"
                 style="{{ App::getLocale() === 'ar' ? 'padding-right: 240px;margin-left: 0px;' : 'padding-left: 240px;margin-right: 0px;' }} transition: all 0s ease 0s;" >

                <x-app.header />

                <main class="p-2">
                    {{ $slot }}
                </main>

            </div>
            <x-toast />
        </div>
        @livewireScriptConfig
        <script type="module">
            // On page load or when changing themes, best to add inline in `head` to avoid FOUC
            if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            };

            function setDarkTheme() {
                document.documentElement.classList.add("dark");
                localStorage.theme = "dark";
            };

            function setLightTheme() {
                document.documentElement.classList.remove("dark");
                localStorage.theme = "light";
            };

            function onThemeSwitcherItemClick(event) {
                const theme = event.target.dataset.theme;

                if (theme === "system") {
                    localStorage.removeItem("theme");
                    setSystemTheme();
                } else if (theme === "dark") {
                    setDarkTheme();
                } else {
                    setLightTheme();
                }
            };

            const themeSwitcherItems = document.querySelectorAll("#theme-switcher");
            themeSwitcherItems.forEach((item) => {
                item.addEventListener("click", onThemeSwitcherItemClick);
            });

            window.addEventListener('showDeleteConfirmation', event => {
                Swal.fire({
                    title: 'هل أنت متأكد',
                    text: 'سيتم الحذف نهائياً',
                    icon: 'warning',
                    showCancelButton: true,
                    cancelButtonText: 'إلغاء',
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#004693',
                    confirmButtonText: 'تأكيد الحذف'
                }).then((result) => {
                    if (result.isConfirmed) {
                        Livewire.dispatch('confirmDelete', {id : event.detail.id});
                    }
                });
            });
            window.addEventListener('swal', event => {
                Swal.fire({
                    position: event.detail.position,
                    icon: event.detail.type,
                    title: event.detail.message,
                    showConfirmButton: false,
                    timer: 1000
                })
            });


        </script>
        <script defer >
            document.addEventListener('alpine:init', () => {
                // Creating component Dropdown
                Alpine.data('collapse', () => ({
                    open: false,
                    toggle(id) {
                        this.open = !this.open;
                       var target = document.getElementById(id)
                        target.classList.toggle("hidden")
                    },
                    activeClass: 'bg-gray-800 text-gray-200',
                }));
                Alpine.data('noticesHandler', () => ({
                    notices: [],
                    visible: [],
                    add(notice) {
                        notice.id = Date.now()
                        this.notices.push(notice)
                        this.fire(notice.id)
                    },
                    fire(id) {
                        this.visible.push(this.notices.find(notice => notice.id == id))
                        const timeShown = 2000 * this.visible.length
                        setTimeout(() => {
                            this.remove(id)
                        }, timeShown)
                    },
                    remove(id) {
                        const notice = this.visible.find(notice => notice.id == id)
                        const index = this.visible.indexOf(notice)
                        this.visible.splice(index, 1)
                    },
                }));
            })


            const MONTH_NAMES = [
                "January",
                "February",
                "March",
                "April",
                "May",
                "June",
                "July",
                "August",
                "September",
                "October",
                "November",
                "December",
            ];
            const MONTH_SHORT_NAMES = [
                "Jan",
                "Feb",
                "Mar",
                "Apr",
                "May",
                "Jun",
                "Jul",
                "Aug",
                "Sep",
                "Oct",
                "Nov",
                "Dec",
            ];
            const DAYS = ["Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat"];

            function date() {
                return {
                    showDatepicker: false,
                    datepickerValue: "",
                    selectedDate: new Date(),
                    dateFormat: "DD-MM-YYYY",
                    month: "",
                    year: "",
                    no_of_days: [],
                    blankdays: [],
                    initDate() {
                        let today;
                        if (this.selectedDate) {
                            today = new Date(Date.parse(this.selectedDate));
                        } else {
                            today = new Date();
                        }
                        this.month = today.getMonth();
                        this.year = today.getFullYear();
                        this.datepickerValue = this.formatDateForDisplay(
                            today
                        );
                    },
                    formatDateForDisplay(date) {
                        let formattedDay = DAYS[date.getDay()];
                        let formattedDate = ("0" + date.getDate()).slice(
                            -2
                        ); // appends 0 (zero) in single digit date
                        let formattedMonth = MONTH_NAMES[date.getMonth()];
                        let formattedMonthShortName =
                            MONTH_SHORT_NAMES[date.getMonth()];
                        let formattedMonthInNumber = (
                            "0" +
                            (parseInt(date.getMonth()) + 1)
                        ).slice(-2);
                        let formattedYear = date.getFullYear();
                        if (this.dateFormat === "DD-MM-YYYY") {
                            return `${formattedDate}-${formattedMonthInNumber}-${formattedYear}`; // 02-04-2021
                        }
                        if (this.dateFormat === "YYYY-MM-DD") {
                            return `${formattedYear}-${formattedMonthInNumber}-${formattedDate}`; // 2021-04-02
                        }
                        if (this.dateFormat === "D d M, Y") {
                            return `${formattedDay} ${formattedDate} ${formattedMonthShortName} ${formattedYear}`; // Tue 02 Mar 2021
                        }
                        return `${formattedDay} ${formattedDate} ${formattedMonth} ${formattedYear}`;
                    },
                    isSelectedDate(date) {
                        const d = new Date(this.year, this.month, date);
                        return this.datepickerValue === this.formatDateForDisplay(d);
                    },
                    isToday(date) {
                        const today = new Date();
                        const d = new Date(this.year, this.month, date);
                        return today.toDateString() === d.toDateString();
                    },
                    getDateValue(date) {
                        let selectedDate = new Date(
                            this.year,
                            this.month,
                            date
                        );
                        this.datepickerValue = this.formatDateForDisplay(
                            selectedDate
                        );

                        // $wire.set('start_date', selectedDate);
                        // this.$refs.date.value = selectedDate.getFullYear() + "-" + ('0' + formattedMonthInNumber).slice(-2) + "-" + ('0' + selectedDate.getDate()).slice(-2);
                        this.isSelectedDate(date);
                        this.showDatepicker = false;
                    },
                    getNoOfDays() {
                        let daysInMonth = new Date(
                            this.year,
                            this.month + 1,
                            0
                        ).getDate();
                        // find where to start calendar day of week
                        let dayOfWeek = new Date(
                            this.year,
                            this.month
                        ).getDay();
                        let blankdaysArray = [];
                        for (var i = 1; i <= dayOfWeek; i++) {
                            blankdaysArray.push(i);
                        }
                        let daysArray = [];
                        for (var i = 1; i <= daysInMonth; i++) {
                            daysArray.push(i);
                        }
                        this.blankdays = blankdaysArray;
                        this.no_of_days = daysArray;
                    },
                };
            }
        </script>
        @stack('scripts')
    </body>
</html>
