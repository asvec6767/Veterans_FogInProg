<?php
include_once('header.php');
?>
    <!-- <main>
        <section class="veteran-info">
            <img src="veteran.jpg" alt="Ветеран Войны" class="veteran-photo">
            <div class="text-info">
                <h2>Иван Иванович Петров</h2>
                <p>Иван Иванович Петров родился 1 января 1920 года. Он служил в армии во время Второй мировой войны и участвовал в нескольких ключевых сражениях. За свою храбрость и мужество был награжден орденами и медалями. После войны Иван Иванович посвятил свою жизнь воспитанию молодежи и патриотическому воспитанию.</p>
            </div>
        </section>
        <section class="map">
            <h2>Место жительства</h2>
            <div id="map" class="yandex-map"></div>
        </section>
        
    </main> -->

    <main>
    <div id="app">

    <div class="modal fade" id="messageModal" tabindex="-1" aria-labelledby="messageModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="messageModalLabel">Ваше послание добавлено!</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    {{ modalMessage }}
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Скрыть</button>
                </div>
            </div>
        </div>
    </div>

        <section class="search-section">
            <div class="search-container">
                <button class="title" @click="toggleSearchForm">
                    Найти героя
                    <i :class="['fa-solid', isShowSearchForm ? 'fa-chevron-up' : 'fa-chevron-down']"></i>
                </button>
                <div class="search-form" v-if="isShowSearchForm">
                    <div class="input-group">
                        <label for="name-search">ФИО</label>
                        <input 
                            type="text" 
                            id="name-search" 
                            v-model="searchParams.name" 
                            placeholder="Введите имя героя"
                            @input="handleSearch"
                        >
                        <span class="input-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="11" cy="11" r="8"></circle>
                                <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                            </svg>
                        </span>
                    </div>
                    
                    <div class="input-group">
                        <label for="year-search">Год рождения</label>
                        <input 
                            type="number" 
                            id="year-search" 
                            v-model="searchParams.year" 
                            placeholder="Год рождения"
                            @input="handleSearch"
                        >
                        <span class="input-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                <line x1="16" y1="2" x2="16" y2="6"></line>
                                <line x1="8" y1="2" x2="8" y2="6"></line>
                                <line x1="3" y1="10" x2="21" y2="10"></line>
                            </svg>
                        </span>
                    </div>
                    
                    <div class="input-group">
                        <label for="location-search">Место призыва</label>
                        <input 
                            type="text" 
                            id="location-search" 
                            v-model="searchParams.location" 
                            placeholder="Город или область"
                            @input="handleSearch"
                        >
                        <span class="input-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                                <circle cx="12" cy="10" r="3"></circle>
                            </svg>
                        </span>
                    </div>
                    
                    <button class="search-button" @click="handleSearch">
                        Найти
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="11" cy="11" r="8"></circle>
                            <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                        </svg>
                    </button>
                </div>
            </div>
        </section>
        
        <section class="memory-section">
            <h2>Память стоит того, чтобы беречь, а благодарность стоит того, чтобы нести </h2>
            <div class="memory-form">
                <textarea 
                    v-model="memoryMessage" 
                    placeholder="Оставьте ваше послание памяти героя..."
                    class="memory-textarea"
                ></textarea>
                <div class="memory-actions">
                    <button class="memory-button" @click="lightCandle">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M9 21h6v-9H9v9z"></path>
                            <path d="M12 3v1"></path>
                            <path d="M18 3v1"></path>
                            <path d="M6 3v1"></path>
                            <path d="M12 12v6"></path>
                            <path d="M12 18v3"></path>
                        </svg>
                        Зажечь свечу
                    </button>
                    <button class="memory-button primary" @click="sendMemoryMessage">
                        Отправить послание
                    </button>
                </div>
            </div>
            
            <div class="memories-list">
                <div class="memory-item" v-for="(memory, index) in memories" :key="index">
                    <div class="memory-author">{{ memory.author }}</div>
                    <div class="memory-text">{{ memory.text }}</div>
                    <div class="memory-date">{{ memory.date }}</div>
                </div>
            </div>
        </section>
        <div class="candle-container" v-if="showCandle">
            <div class="candle">
                <div class="flame">
                  <div class="shadows"></div>
                  <div class="top"></div>
                  <div class="middle"></div>
                  <div class="bottom"></div>
            </div>
            <div class="wick"></div>
            <div class="wax"></div>
        </div>
        </div>
    </div>

</main>

    <script src="https://api-maps.yandex.ru/2.1/?lang=ru_RU" type="text/javascript"></script>
    <script>
        // ymaps.ready(init);
        // function init() {
        //     var myMap = new ymaps.Map("map", {
        //         center: [55.751574, 37.573856], // Координаты для центра карты (Москва)
        //         zoom: 10
        //     });

        //     var myPlacemark = new ymaps.Placemark([55.751574, 37.573856], {
        //         balloonContent: 'Дом Ивана Ивановича Петрова'
        //     });

        //     myMap.geoObjects.add(myPlacemark);
        // }

        const { createApp } = Vue;
    
        createApp({
            data() {
                return {
                    searchParams: {
                        name: '',
                        year: '',
                        location: ''
                    },
                    memoryMessage: null,
                    memories: [
                        {
                            author: 'Анна Смирнова',
                            text: 'Вечная память героям! Спасибо за нашу свободу!',
                            date: '12.05.2023'
                        },
                        {
                            author: 'Дмитрий Иванов',
                            text: 'Низкий поклон за ваш подвиг. Мы помним!',
                            date: '09.05.2023'
                        }
                    ],

                    showCandle: false,
                    isShowSearchForm: false,
                }
            },
            
            methods: {
                // handleSearch() {
                //     console.log('Поиск:', this.searchParams);
                // },

                toggleSearchForm() {
                    this.isShowSearchForm = !this.isShowSearchForm;
                },

                lightCandle() {
                    this.showCandle = true;
                },

                showModal(message) {
                    this.modalMessage = message;
                    const modal = new bootstrap.Modal(document.getElementById('messageModal'));
                    modal.show();
                },

                sendMemoryMessage() {
                    if (this.memoryMessage && this.memoryMessage.trim()) {
                        const newMemory = {
                            author: 'Аноним', 
                            text: this.memoryMessage,
                            date: new Date().toLocaleDateString()
                        };
                        this.memories.unshift(newMemory);
                        this.memoryMessage = '';
                        this.showModal('Спасибо, что поделились памятью о герое Великой Отечественной войны. Ваш вклад помогает сохранить историю для будущих поколений.');
                    }
                }
            },
        }).mount('#app');
    </script>

<?
include_once('footer.php');
?>
