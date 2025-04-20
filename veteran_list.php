<? include_once('header.php'); 

// Подключение к БД и получение информации о ветеране
require_once 'db.php';  // Подключаем файл с функциями для работы с БД

$conn = connectDB(); //Подключение к бд
if (!$conn) {
  die("Ошибка подключения к БД.");  //Завершаем выполнение скрипта, если нет подключения
}?>
<? $veterans = getAllVeterans($conn);

usort($veterans, function($a, $b) {
    return strcmp($a['name'], $b['name']);
});?>
<link rel="stylesheet" href="/css/veteran.css">
<!-- <script src="/js/veteran_script.js"></script> -->

<? /**Страница списка всех Ветеранов */?>
    <main class="person-main">
        <div id="searchComponent">
        <section class="search-section">
            <div class="search-container">
                <button class="title" @click="toggleSearchForm">
                    Найти героя
                    <i :class="['fa-solid', isShowSearchForm ? 'fa-chevron-up' : 'fa-chevron-down']"></i>
                </button>
                <div v-if="isLoading" class="spinner-border text-primary mt-5 not-person" style="width: 10rem; height: 10rem;" role="status"></div>
                <div class="search-form" v-if="isShowSearchForm && !isLoading">
                    <div class="input-group" style="width: 100%;">
                        <label for="name-search">ФИО</label>
                        <input 
                            type="text" 
                            id="name-search" 
                            v-model="searchParams.name" 
                            placeholder="Введите ФИО героя"
                        >
                        <span class="input-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="11" cy="11" r="8"></circle>
                                <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                            </svg>
                        </span>
                    </div>

                    <!-- <div class="input-group">
                        <label for="year-search">Год рождения</label>
                        <input 
                            type="text" 
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
                    </div> -->

                    <div class="btn-search">
                        <button class="btn btn-primary" @click="handleSearch">
                            Найти
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="11" cy="11" r="8"></circle>
                                <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                            </svg>
                        </button>

                        <button class="btn btn-danger" @click="resetSearch">
                            Сбросить
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <line x1="18" y1="6" x2="6" y2="18"></line>
                                <line x1="6" y1="6" x2="18" y2="18"></line>
                            </svg>
                        </button>
                    </div>

                </div>

                <div class="not-person" v-if="persons.length === 0 && !isLoading && isShowSearchForm">
                    <span>Ничего не найдено</span>
                </div>


            </div>
            
        </section>

        <ul class="person-list mt-3" v-if="persons.length > 0">
            <li v-for="person in persons" :key="person.id" class="mb-5 mt-5 person" >
                <template v-if="person.image_url">
                    <img :src="'/img/veteran/' + person.image_url" class="person__img">
                </template>
                <template v-else>
                <i class="fa-solid fa-user-large fa-2xl person__img d-flex justify-content-center align-items-center" style="color:rgb(150, 160, 177); font-size: 5rem; width: 120px;"></i>
                </template>
                <div class="person__desc">
                    <a :href="'/veteran/' + person.id" class="person__link mb-2">{{ person.name }}</a>
                    <span>{{person.date}} гг.</span>
                    <span>{{person.desc}}</span>
                </div>
            </li>
        </ul>



        </div>
    </main>


<?php
if ($conn) {
  $conn = null; //Закрываем соединение с БД
}
?>

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
            isShowSearchForm: false,
            isLoading: false,

            allPersons: <?php echo json_encode($veterans, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE); ?>,

            persons: <?php echo json_encode($veterans, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE); ?>,
        }
    },
    
    methods: {
        toggleSearchForm() {
            this.isShowSearchForm = !this.isShowSearchForm;
        },
        
        handleSearch() {
            if (this.searchParams.name && this.searchParams.name.length < 3) {
                this.persons = []; 
                this.isLoading = false;
                return;
            }

            this.isLoading = true;

            setTimeout(() => {
                this.persons = this.allPersons.filter(person => {
                    const nameMatch = !this.searchParams.name || 
                        person.name.toLowerCase().includes(this.searchParams.name.toLowerCase());

                    return nameMatch;
                });

                this.isLoading = false;
            }, 500); 
        },

        resetSearch() {
            this.searchParams = {
                name: '',
                year: '',
                location: ''
            };
            this.persons = [...this.allPersons].sort((a, b) => a.name.localeCompare(b.name));
        }
    }, 

    mounted() {

    }
}).mount('#searchComponent');
</script>
<?/*php
echo '<div style="background-color:rgb(13, 34, 102); color: white; font-size:18px; font-weight: 500; padding: 10px; border: 1px solid #ccc; border-radius: 5px;">';
echo '<pre>';
var_dump($veterans);
echo '</pre>';
echo '</div>';
*/?>
<? include_once('footer.php'); ?>