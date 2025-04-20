
<?php
$title = 'Страницы памяти';
include_once('header.php');
?>
    <main>
    <div class="fullscreen-slider">
        <div class="swiper mr-5 ml-5 mt-2">
              <div class="swiper-wrapper">
                <div class="swiper-slide d-flex justify-content-center mb-5 w-25"><img class="img-photo rounded-3" style="height:240px"src="/img/img1.jpg"></div>
                <div class="swiper-slide d-flex justify-content-center mb-5 w-25"><img class="img-photo rounded-3" style="height:240px" src="/img/img2.jpg"></div>
                <div class="swiper-slide d-flex justify-content-center mb-5 w-25"><img class="img-photo rounded-3" style="height:240px" src="/img/img3.jpg"></div>
                <div class="swiper-slide d-flex justify-content-center mb-5 w-25"><img class="img-photo rounded-3" style="height:240px" src="/img/img4.jpg"></div>
                <div class="swiper-slide d-flex justify-content-center mb-5 w-25"><img class="img-photo rounded-3" style="height:240px" src="/img/img5.jpg"></div>
                <!-- <div class="swiper-slide d-flex justify-content-center mb-5 w-25"><img class="img-photo rounded-3" src="/img/img6.jpg"></div>
                <div class="swiper-slide d-flex justify-content-center mb-5 w-25"><img class="img-photo rounded-3" src="/img/img7.jpg"></div> -->
                <div class="swiper-slide d-flex justify-content-center mb-5 w-25"><img class="img-photo rounded-3" style="height:240px"  src="/img/img8.jpg"></div>
              </div>
              <!-- <div class="swiper-button-prev"></div>
              <div class="swiper-button-next"></div> -->
        </div>
    </div>
       
 <!-- контейнер карточек -->
 <h2 class="losung">Ваш подвиг бессмертен – наша память вечна</h2>
        <div class="cards-container">
        <!-- Карточка 1 -->
        <div class="card">
        <img src="/img/veteran/avericheva.jpg" class="card-image">
            <div class="card-overlay">
                <div class="card-text">Аверичева Софья Петровна<br> Дата рождения: 10 сентября 1914<br>
                Дата смерти: 10 мая 2015</div>
            </div>
            <a href="https://example.com/link1" class="card-link" aria-label="Перейти к разделу 1"></a>
        </div>
        
        <!-- Карточка 2 -->
        <div class="card">
        <img src="/img/veteran/agibalov.jpg" class="card-image">
          
            <div class="card-overlay">
                <div class="card-text">Агибалов Леонид Дмитриевич <br>Дата рождения: 25 сентября 1920<br>
                Дата смерти: 7 февраля 1986</div>
            </div>
            <a href="https://example.com/link2" class="card-link" aria-label="Перейти к разделу 2"></a>
        </div>
        
        <!-- Карточка 3 -->
        <div class="card">
        <img src="/img/veteran/adiyanov.jpg" class="card-image">
            <div class="card-overlay">
                <div class="card-text">Адиянов Василий Савельевич <br>Дата рождения: 1916<br>
                Дата смерти: 19 апреля 1992</div>
            </div>
            <a href="https://example.com/link3" class="card-link" aria-label="Перейти к разделу 3"></a>
        </div>
        
        <!-- Карточка 4 -->
        <div class="card">
        <img src="/img/veteran/abashin2.jpg" class="card-image">
            <div class="card-overlay">
                <div class="card-text">Абашин Николай Борисович <br>Дата рождения: 12 декабря 1922<br>
Место рождения: в деревня Петелино Ленинского района Тульской области<br>
Дата смерти: 9 сентября 1989<br>
Место смерти: г. Львов </div>
            </div>
            <a href="https://example.com/link4" class="card-link" aria-label="Перейти к разделу 4"></a>
        </div>
        
        <!-- Карточка 5 -->
        <div class="card">
        <img src="/img/veteran/abakumov.jpg" class="card-image">
            <div class="card-overlay">
                <div class="card-text">Абакумов Александр Степанович <br>Дата рождения: 19 августа 1918<br>
Место рождения: Токаревский район Тамбовская область с. Абакумовка<br>
Дата смерти: 13 марта 2006<br>
Место смерти: Тамбовская область</div>
            </div>
            <a href="https://example.com/link5" class="card-link" aria-label="Перейти к разделу 5"></a>
        </div>
        
        <!-- Карточка 6 -->
        <div class="card">
        <img src="/img/veteran/yaroslavceva.jpg" class="card-image">
            <div class="card-overlay">
                <div class="card-text">Ярославцева Мария Ивановна<br> Дата рождения: 3 августа 1924<br>
                Дата смерти: 2 июля 1998</div>
            </div>
            <a href="https://example.com/link6" class="card-link" aria-label="Перейти к разделу 6"></a>
        </div>
    </div>



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
        

        <?include_once('comment.php');?>


        <!-- <section class="memory-section">
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
        </section> -->
        
    </div>

</main>

    <script>
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
                    isLoading: false,
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


        const swiper = new Swiper('.swiper', {
            speed: 28000,
            spaceBetween: 10, 
            slidesPerView: 'auto', 
            navigation: {
                nextEl: '.swiper-button-next',
                prevEl: '.swiper-button-prev',
            },
             loop: true, 
            autoplay: {
                delay: 1000, 
                disableOnInteraction: false,
            },
        });
    </script>

<?
include_once('footer.php');
?>
