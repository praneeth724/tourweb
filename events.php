<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Events & Workshops - CeylonEcoTrails</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');
        body {
            font-family: 'Poppins', sans-serif;
        }
        .nav-scrolled {
            background-color: rgba(22, 101, 52, 0.95);
            backdrop-filter: blur(10px);
        }
        .calendar-day {
            cursor: pointer;
            transition: all 0.3s;
        }
        .calendar-day:hover {
            background-color: #dcfce7;
        }
        .calendar-day.has-event {
            background-color: #86efac;
            font-weight: bold;
        }
        .calendar-day.selected {
            background-color: #16a34a;
            color: white;
        }
    </style>
</head>
<body class="bg-gray-50">
    <?php include 'navbar.php'; ?>

    <!-- Hero Section -->
    <section class="pt-32 pb-20 bg-gradient-to-br from-green-700 to-green-900 text-white">
        <div class="container mx-auto px-4 text-center">
            <h1 class="text-5xl md:text-6xl font-bold mb-6">Events & Workshops</h1>
            <p class="text-xl md:text-2xl text-green-100">Join our community events and conservation initiatives</p>
        </div>
    </section>

    <!-- Calendar Section -->
    <section class="py-20 bg-white">
        <div class="container mx-auto px-4">
            <div class="max-w-4xl mx-auto">
                <div class="bg-white rounded-lg shadow-xl p-8">
                    <div class="flex justify-between items-center mb-8">
                        <button id="prevMonth" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition">
                            <i class="fas fa-chevron-left"></i> Previous
                        </button>
                        <h2 class="text-3xl font-bold text-gray-800" id="currentMonth"></h2>
                        <button id="nextMonth" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition">
                            Next <i class="fas fa-chevron-right"></i>
                        </button>
                    </div>
                    <div class="grid grid-cols-7 gap-2 mb-4">
                        <div class="text-center font-bold text-gray-700 p-2">Sun</div>
                        <div class="text-center font-bold text-gray-700 p-2">Mon</div>
                        <div class="text-center font-bold text-gray-700 p-2">Tue</div>
                        <div class="text-center font-bold text-gray-700 p-2">Wed</div>
                        <div class="text-center font-bold text-gray-700 p-2">Thu</div>
                        <div class="text-center font-bold text-gray-700 p-2">Fri</div>
                        <div class="text-center font-bold text-gray-700 p-2">Sat</div>
                    </div>
                    <div id="calendarDays" class="grid grid-cols-7 gap-2"></div>
                </div>
            </div>
        </div>
    </section>

    <!-- Upcoming Events -->
    <section class="py-20 bg-gray-50">
        <div class="container mx-auto px-4">
            <div class="text-center mb-16">
                <h2 class="text-4xl font-bold text-gray-800 mb-4">Upcoming Events</h2>
                <p class="text-gray-600 text-lg">Exciting conservation and adventure activities</p>
            </div>
            <div class="grid md:grid-cols-2 gap-8" id="events-list">

                <!-- Event 1 -->
                <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                    <div class="bg-green-600 text-white p-6">
                        <div class="text-4xl font-bold">15</div>
                        <div class="text-lg">January 2025</div>
                    </div>
                    <div class="p-6">
                        <div class="flex items-center gap-2 text-green-600 mb-2">
                            <i class="fas fa-tag"></i>
                            <span class="font-semibold">Conservation Workshop</span>
                        </div>
                        <h3 class="text-2xl font-bold mb-3 text-gray-800">Reforestation Volunteer Day</h3>
                        <p class="text-gray-600 mb-4">Join us for a hands-on tree planting workshop in collaboration with local communities. Help restore degraded forest areas and learn about native species.</p>
                        <div class="space-y-2 text-gray-600 mb-4">
                            <div><i class="fas fa-clock text-green-600"></i> 8:00 AM - 4:00 PM</div>
                            <div><i class="fas fa-map-marker-alt text-green-600"></i> Knuckles Conservation Forest</div>
                            <div><i class="fas fa-users text-green-600"></i> Max 30 participants</div>
                        </div>
                        <a href="booking.html?event=reforestation" class="block text-center bg-green-600 text-white px-6 py-3 rounded-full hover:bg-green-700 transition">Register Now - Free</a>
                    </div>
                </div>

                <!-- Event 2 -->
                <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                    <div class="bg-blue-600 text-white p-6">
                        <div class="text-4xl font-bold">22</div>
                        <div class="text-lg">January 2025</div>
                    </div>
                    <div class="p-6">
                        <div class="flex items-center gap-2 text-blue-600 mb-2">
                            <i class="fas fa-tag"></i>
                            <span class="font-semibold">Educational Workshop</span>
                        </div>
                        <h3 class="text-2xl font-bold mb-3 text-gray-800">Wildlife Photography Masterclass</h3>
                        <p class="text-gray-600 mb-4">Learn professional wildlife photography techniques from award-winning nature photographers. Includes field practice and portfolio review.</p>
                        <div class="space-y-2 text-gray-600 mb-4">
                            <div><i class="fas fa-clock text-blue-600"></i> 9:00 AM - 5:00 PM</div>
                            <div><i class="fas fa-map-marker-alt text-blue-600"></i> Sinharaja Rainforest</div>
                            <div><i class="fas fa-users text-blue-600"></i> Max 15 participants</div>
                        </div>
                        <a href="booking.html?event=photography" class="block text-center bg-blue-600 text-white px-6 py-3 rounded-full hover:bg-blue-700 transition">Register Now - $79</a>
                    </div>
                </div>

                <!-- Event 3 -->
                <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                    <div class="bg-yellow-600 text-white p-6">
                        <div class="text-4xl font-bold">05</div>
                        <div class="text-lg">February 2025</div>
                    </div>
                    <div class="p-6">
                        <div class="flex items-center gap-2 text-yellow-600 mb-2">
                            <i class="fas fa-tag"></i>
                            <span class="font-semibold">Festival</span>
                        </div>
                        <h3 class="text-2xl font-bold mb-3 text-gray-800">Eco-Tourism Festival 2025</h3>
                        <p class="text-gray-600 mb-4">Annual celebration of sustainable tourism with cultural performances, local crafts market, nature walks, and environmental talks by experts.</p>
                        <div class="space-y-2 text-gray-600 mb-4">
                            <div><i class="fas fa-clock text-yellow-600"></i> 10:00 AM - 8:00 PM</div>
                            <div><i class="fas fa-map-marker-alt text-yellow-600"></i> Kandy City Center</div>
                            <div><i class="fas fa-users text-yellow-600"></i> Open to all</div>
                        </div>
                        <a href="booking.html?event=festival" class="block text-center bg-yellow-600 text-white px-6 py-3 rounded-full hover:bg-yellow-700 transition">Register Now - Free</a>
                    </div>
                </div>

                <!-- Event 4 -->
                <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                    <div class="bg-purple-600 text-white p-6">
                        <div class="text-4xl font-bold">14</div>
                        <div class="text-lg">February 2025</div>
                    </div>
                    <div class="p-6">
                        <div class="flex items-center gap-2 text-purple-600 mb-2">
                            <i class="fas fa-tag"></i>
                            <span class="font-semibold">Educational Workshop</span>
                        </div>
                        <h3 class="text-2xl font-bold mb-3 text-gray-800">Traditional Herbal Medicine Walk</h3>
                        <p class="text-gray-600 mb-4">Discover ancient Ayurvedic wisdom as local healers teach you to identify medicinal plants and their uses in traditional Sri Lankan medicine.</p>
                        <div class="space-y-2 text-gray-600 mb-4">
                            <div><i class="fas fa-clock text-purple-600"></i> 7:00 AM - 12:00 PM</div>
                            <div><i class="fas fa-map-marker-alt text-purple-600"></i> Matale Herbal Gardens</div>
                            <div><i class="fas fa-users text-purple-600"></i> Max 20 participants</div>
                        </div>
                        <a href="booking.html?event=herbal" class="block text-center bg-purple-600 text-white px-6 py-3 rounded-full hover:bg-purple-700 transition">Register Now - $45</a>
                    </div>
                </div>

                <!-- Event 5 -->
                <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                    <div class="bg-red-600 text-white p-6">
                        <div class="text-4xl font-bold">20</div>
                        <div class="text-lg">February 2025</div>
                    </div>
                    <div class="p-6">
                        <div class="flex items-center gap-2 text-red-600 mb-2">
                            <i class="fas fa-tag"></i>
                            <span class="font-semibold">Adventure Event</span>
                        </div>
                        <h3 class="text-2xl font-bold mb-3 text-gray-800">Full Moon Jungle Night Trek</h3>
                        <p class="text-gray-600 mb-4">Experience the rainforest after dark under the full moon. Spot nocturnal wildlife, hear night sounds, and camp under the stars.</p>
                        <div class="space-y-2 text-gray-600 mb-4">
                            <div><i class="fas fa-clock text-red-600"></i> 6:00 PM - 7:00 AM (next day)</div>
                            <div><i class="fas fa-map-marker-alt text-red-600"></i> Sinharaja Rainforest</div>
                            <div><i class="fas fa-users text-red-600"></i> Max 12 participants</div>
                        </div>
                        <a href="booking.html?event=nighttrek" class="block text-center bg-red-600 text-white px-6 py-3 rounded-full hover:bg-red-700 transition">Register Now - $99</a>
                    </div>
                </div>

                <!-- Event 6 -->
                <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                    <div class="bg-green-600 text-white p-6">
                        <div class="text-4xl font-bold">08</div>
                        <div class="text-lg">March 2025</div>
                    </div>
                    <div class="p-6">
                        <div class="flex items-center gap-2 text-green-600 mb-2">
                            <i class="fas fa-tag"></i>
                            <span class="font-semibold">Conservation Workshop</span>
                        </div>
                        <h3 class="text-2xl font-bold mb-3 text-gray-800">Beach Cleanup & Marine Conservation</h3>
                        <p class="text-gray-600 mb-4">Join our coastal cleanup initiative and learn about marine ecosystems, plastic pollution, and turtle conservation efforts in Sri Lanka.</p>
                        <div class="space-y-2 text-gray-600 mb-4">
                            <div><i class="fas fa-clock text-green-600"></i> 6:00 AM - 1:00 PM</div>
                            <div><i class="fas fa-map-marker-alt text-green-600"></i> Rekawa Beach</div>
                            <div><i class="fas fa-users text-green-600"></i> Max 40 participants</div>
                        </div>
                        <a href="booking.html?event=beach" class="block text-center bg-green-600 text-white px-6 py-3 rounded-full hover:bg-green-700 transition">Register Now - Free</a>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- Workshops Section -->
    <section class="py-20 bg-white">
        <div class="container mx-auto px-4">
            <div class="text-center mb-16">
                <h2 class="text-4xl font-bold text-gray-800 mb-4">Regular Workshops</h2>
                <p class="text-gray-600 text-lg">Skill-building programs held throughout the year</p>
            </div>
            <div class="grid md:grid-cols-3 gap-8">
                <div class="bg-gray-50 p-6 rounded-lg shadow hover:shadow-xl transition">
                    <i class="fas fa-compass text-green-600 text-4xl mb-4"></i>
                    <h3 class="text-xl font-bold mb-3 text-gray-800">Wilderness Navigation</h3>
                    <p class="text-gray-600 mb-4">Learn map reading, compass use, and GPS navigation for safe trekking.</p>
                    <p class="text-sm text-gray-500">Monthly | $35 per person</p>
                </div>
                <div class="bg-gray-50 p-6 rounded-lg shadow hover:shadow-xl transition">
                    <i class="fas fa-first-aid text-red-600 text-4xl mb-4"></i>
                    <h3 class="text-xl font-bold mb-3 text-gray-800">Wilderness First Aid</h3>
                    <p class="text-gray-600 mb-4">Essential first aid skills for outdoor emergencies and remote locations.</p>
                    <p class="text-sm text-gray-500">Quarterly | $120 per person</p>
                </div>
                <div class="bg-gray-50 p-6 rounded-lg shadow hover:shadow-xl transition">
                    <i class="fas fa-leaf text-green-600 text-4xl mb-4"></i>
                    <h3 class="text-xl font-bold mb-3 text-gray-800">Nature Journaling</h3>
                    <p class="text-gray-600 mb-4">Document your outdoor experiences through sketching and creative writing.</p>
                    <p class="text-sm text-gray-500">Monthly | $25 per person</p>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-20 bg-green-700 text-white">
        <div class="container mx-auto px-4 text-center">
            <h2 class="text-4xl font-bold mb-6">Stay Updated on Events</h2>
            <p class="text-xl text-green-100 mb-8">Subscribe to our newsletter for event announcements and conservation updates</p>
            <form class="max-w-md mx-auto flex gap-2">
                <input type="email" placeholder="Enter your email" class="flex-1 px-6 py-3 rounded-full text-gray-800" required>
                <button type="submit" class="bg-white text-green-800 px-8 py-3 rounded-full hover:bg-gray-100 transition font-semibold">Subscribe</button>
            </form>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white py-12">
        <div class="container mx-auto px-4">
            <div class="grid md:grid-cols-4 gap-8">
                <div>
                    <h3 class="text-2xl font-bold mb-4"><i class="fas fa-mountain text-green-400"></i> CeylonEcoTrails</h3>
                    <p class="text-gray-400">Sustainable trekking and eco-tourism in the heart of Sri Lanka.</p>
                </div>
                <div>
                    <h4 class="text-lg font-bold mb-4">Quick Links</h4>
                    <ul class="space-y-2">
                        <li><a href="about.html" class="text-gray-400 hover:text-green-400 transition">About Us</a></li>
                        <li><a href="tours.html" class="text-gray-400 hover:text-green-400 transition">Tours</a></li>
                        <li><a href="events.html" class="text-gray-400 hover:text-green-400 transition">Events</a></li>
                        <li><a href="gallery.html" class="text-gray-400 hover:text-green-400 transition">Gallery</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-lg font-bold mb-4">Contact</h4>
                    <ul class="space-y-2 text-gray-400">
                        <li><i class="fas fa-map-marker-alt text-green-400"></i> Kandy, Sri Lanka</li>
                        <li><i class="fas fa-phone text-green-400"></i> +94 77 123 4567</li>
                        <li><i class="fas fa-envelope text-green-400"></i> info@ceylonecotrails.lk</li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-lg font-bold mb-4">Follow Us</h4>
                    <div class="flex space-x-4">
                        <a href="#" class="text-gray-400 hover:text-green-400 text-2xl transition"><i class="fab fa-facebook"></i></a>
                        <a href="#" class="text-gray-400 hover:text-green-400 text-2xl transition"><i class="fab fa-instagram"></i></a>
                        <a href="#" class="text-gray-400 hover:text-green-400 text-2xl transition"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="text-gray-400 hover:text-green-400 text-2xl transition"><i class="fab fa-youtube"></i></a>
                    </div>
                </div>
            </div>
            <div class="border-t border-gray-800 mt-8 pt-8 text-center text-gray-400">
                <p>&copy; 2024 CeylonEcoTrails. All rights reserved. Designed with care for nature.</p>
            </div>
        </div>
    </footer>

    <script src="script.js"></script>
    <script>
        // Calendar functionality
        let currentDate = new Date();
        const monthNames = ["January", "February", "March", "April", "May", "June",
            "July", "August", "September", "October", "November", "December"];

        // Event dates (day of month)
        const eventDates = {
            0: [15, 22], // January
            1: [5, 14, 20], // February
            2: [8] // March
        };

        function renderCalendar() {
            const year = currentDate.getFullYear();
            const month = currentDate.getMonth();

            document.getElementById('currentMonth').textContent = `${monthNames[month]} ${year}`;

            const firstDay = new Date(year, month, 1).getDay();
            const daysInMonth = new Date(year, month + 1, 0).getDate();

            let calendarHTML = '';

            // Empty cells for days before month starts
            for (let i = 0; i < firstDay; i++) {
                calendarHTML += '<div class="p-4"></div>';
            }

            // Days of the month
            for (let day = 1; day <= daysInMonth; day++) {
                const hasEvent = eventDates[month] && eventDates[month].includes(day);
                const classes = `calendar-day p-4 text-center rounded-lg ${hasEvent ? 'has-event' : ''}`;
                calendarHTML += `<div class="${classes}" data-day="${day}">${day}</div>`;
            }

            document.getElementById('calendarDays').innerHTML = calendarHTML;
        }

        document.getElementById('prevMonth').addEventListener('click', () => {
            currentDate.setMonth(currentDate.getMonth() - 1);
            renderCalendar();
        });

        document.getElementById('nextMonth').addEventListener('click', () => {
            currentDate.setMonth(currentDate.getMonth() + 1);
            renderCalendar();
        });

        renderCalendar();
    </script>
</body>
</html>
