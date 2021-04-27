<?
use app\models\Services;
use app\models\Services_groups;
use yii\helpers\Html;
?>   
  





<div class="page">
  <div data-num=1 class="num">1</div>
  <div data-num=2 class="num">2</div>
  <div data-num=3 class="num">3</div>
  <div data-num=4 class="num">4</div>
  <div data-num=5 class="num">5</div>
  <div data-num=6 class="num">6</div>
  <div data-num=7 class="num">7</div>
  <div data-num=8 class="num">8</div>
  <div data-num=9 class="num">9</div>
  <div data-num=10 class="num">10</div>
</div>
<!-- Создать кнопку, которая будет выводить все услуги независимо от id -->
<!-- <div class="paginator" onclick="pagination(event)"></div> -->
<!-- Разместить блок 'paginator' снизу фрейма -->

<link rel='stylesheet' href='/web/css/Services.css'>
<!-- <link rel='stylesheet' href='/css/Services.css'> -->

<div>
  <div id="mainContainer" class="services">
    <div id="groupList" class="services__groups-container">
      <div id="serviceSearch" class="services__search-container">
        <input id="searchInput" type="text" class="services__search-input">
        <img src="/web/img/search.png" alt="" class = 'icon'>
      </div>
      <ul id="service_list_id" class="services__list">
      <li id = 'singleService' class='services__list-container services__list-container1'><button class='services__list-button' onclick = getAllServices() id = "serviceMain_button">Все услуги</button>  </li>
      <div id="services_list_0" class="services__list-cont"></div>
      <?php 
        $newId = 1;
        foreach ($servicesGroupsTableArray as $items): ?>
		      <li id="singleService" class="services__list-container">
            <button id="services_button_<?=$items["id"]?>" class='services__list-button' onclick="getServices(<?= $items['id'] ?>);" service-group='<?=$items["id"]?>'><?=HTML::encode($items["nameServiceGroup"])?>
            </button>
            <div id="services_list_<?=$items["id"]?>" class="services__list-cont"></div>
          </li>
          <? $newId++; ?>
	    <?php endforeach; ?>
      
      </ul> 
      <div class="paginator" onclick="pagination(event)"></div>
    </div>
    
    <div id="servicesContainer" class="services__result-container">
          
    </div>
  </div>
</div>


<script>

//Php массив переводим в js
const servicesTableArray = <?=json_encode($servicesTableArray)?>;
const servicesGroupsTableArray = <?=json_encode($servicesGroupsTableArray)?>;

console.log(servicesTableArray.length);





///////////////////////////////////////////////////


const mainContainer = document.getElementById("mainContainer");
const servicesContainer = document.getElementById("servicesContainer");
const buttonsContainers = document.querySelectorAll(".services__list-cont");
const searchInput = document.getElementById("searchInput");
const groupButtons = document.querySelectorAll(".services__list-button");
const groupList = document.getElementById("groupList");
const serviceSearch = document.getElementById("serviceSearch");

const maxElementHeight = 50;
const collapseWidth = 991;
var mobileScreen = false;

// Функция подгоняет высоту mainContainer, в котором содержатся кнопки и строчка поиска под их общую высоту 
/*function fitMedia(){
var sumOfHeight = 0;
groupButtons.forEach(function(elem, key){
  if(elem.style.display != 'none' && mobileScreen == true){
    sumOfHeight += elem.offsetHeight;}
}) 
  if(mobileScreen == true)
    mainContainer.style.height = sumOfHeight + serviceSearch.offsetHeight + 30; 
}
*/
// Проверка разрешения экрана по width, флаг для настройки адаптивности
function checkTypeScreen() {
  if (mainContainer.offsetWidth <= collapseWidth) mobileScreen = true;
  else mobileScreen = false;
}

// Функция проверяет пустой ли контейнер с услугами и вписывает туда ниже приведенную информацию
function checkEmptyServicesContainer() {
  if (!servicesContainer.childNodes.length && mobileScreen == false) {
    const infoBlock = document.createElement("div");
    infoBlock.classList.add("services__empty");
    infoBlock.innerText = "Здесь будет отображаться информация об услугах";
    servicesContainer.append(infoBlock);
  }
}

//Формирование элементов модуля
function createServiceElement(parent, value) {
  const showBlockWrap = document.createElement("div");
        showBlockWrap.classList.add("services__result-item-container"); // нужно прибавлять id-шник(или как в примере сверху добавить data-num) при генерации и плюсовать его
        parent.append(showBlockWrap);
        
        const resultItemRow = document.createElement("div");
        resultItemRow.classList.add("services__container-row");
        showBlockWrap.append(resultItemRow);

        const resultItemCol_1 = document.createElement("div");
        resultItemCol_1.classList.add("services__container-leftbar");
        resultItemRow.append(resultItemCol_1);

        const resultItemTitle = document.createElement("a");
        resultItemTitle.classList.add("services__container-title");
        resultItemTitle.innerText = value["nameService"];
        resultItemCol_1.append(resultItemTitle);

        const resultItemText = document.createElement("p");
        resultItemText.classList.add("services__container-info");
        resultItemText.innerText = value["serviceInfo"];
        resultItemCol_1.append(resultItemText);
        
        collapseText(resultItemText);

        const resultItemCol_2 = document.createElement("div");
        resultItemCol_2.classList.add("services__container-rightbar");
        resultItemRow.append(resultItemCol_2,);

        const resultItemPrice = document.createElement("span");
        resultItemPrice.classList.add("services__container-price");
        resultItemPrice.innerText = value["priceService"].toLocaleString('ru-RU', { style: 'currency', currency: 'RUB' });
        resultItemCol_2.append(resultItemPrice);

}

// Функция закрывает(удаляет) предыдущий блок с информацией о сервисе, который сформировали работает при mobile = true
function clearAllServices() {
  if (servicesContainer) servicesContainer.innerHTML = "";

  if (buttonsContainers) {
    buttonsContainers.forEach(function(elem, key) {elem.innerHTML = "";});
  }
}

// Вывод услуг по нажатию кнопки
function getServices(id = null) {
  const button = document.getElementById(`services_button_${id}`);
  const list = document.getElementById(`services_list_${id}`);

  const lastActiveButton = document.querySelector(".services__list-button.active");

  //Проверка на уже открытое поле
  if (lastActiveButton) lastActiveButton.classList.remove('active');

  clearAllServices();

  if (button && servicesContainer) {
    const serviceGroupId = button.getAttribute("service-group");

    button.classList.add('active');

    if (lastActiveButton == button && list) {
      list.innerHTML = "";
      button.classList.remove('active');
    }
    else {
      servicesTableArray.forEach(function(value, key) {
      if (value["idServiceGroup"] == serviceGroupId) {
        if (mobileScreen && list) createServiceElement(list, value);
        else {
          createServiceElement(servicesContainer, value);
        }
      }
    });
    }

    checkEmptyServicesContainer();
  }

}
// Обработчик кнопки получения всех услуг
function getAllServices(id = null) {
  const button = document.getElementById(`serviceMain_button`);
  const list = document.getElementById(`services_list_0`);

  const lastActiveButton = document.querySelector(".services__list-button.active");

  //Проверка на уже открытое поле
  if (lastActiveButton) lastActiveButton.classList.remove('active');

  clearAllServices();

  if (button && servicesContainer) {
    const serviceGroupId = button.getAttribute("service-group");

    button.classList.add('active');

    if (lastActiveButton == button && list) {
      console.log(1);
      list.innerHTML = "";
      button.classList.remove('active');
    }
    else {
      servicesTableArray.forEach(function(value, key) {
      if (true) {
        if (mobileScreen && list) createServiceElement(list, value);
        else {
          createServiceElement(servicesContainer, value);
        }
      }
    });
    }

    checkEmptyServicesContainer();
  }

}
// -----------------------------

clearAllServices();
checkTypeScreen();
checkEmptyServicesContainer();
//fitMedia();


window.addEventListener("resize", () => {
  checkTypeScreen();
  getServices();
});

//Осуществляет логику поиска
searchInput.addEventListener("change", () => {
  var searchText = searchInput.value.trim();

  [].slice.call(groupButtons).forEach(function(elem) {
    const elemText = elem.innerText.toLowerCase();
    if (elemText.indexOf(searchText.toLowerCase()) == 0 || searchText == "") {
      elem.style.display = "";
    }
    else elem.style.display = "none";
  }); 
 // fitMedia();
});

// Если высота блока текста больше дефолта, то добавляем кнопку, которая обрабатывает блок, раскрывая его по фул-контенту
function collapseText(object) {
  if (!object) return;

  const objectHeight = object.offsetHeight;
  
  if (objectHeight >= maxElementHeight) {
    object.setAttribute("max-height", objectHeight);
    object.style.maxHeight = maxElementHeight + "px";

    const collapseButton = document.createElement("button");
    collapseButton.innerText = "Подробнее";
    collapseButton.classList.add("service__text-collapse");
    object.parentNode.append(collapseButton);

    collapseButton.addEventListener("click", e => {
      e.preventDefault();
      if (object.offsetHeight == maxElementHeight) {
        if (object.hasAttribute("max-height")) object.style.maxHeight = object.getAttribute("max-height") + "px";
        else object.style.maxHeight = "max-content";
      }
      else {
        object.style.maxHeight = maxElementHeight + "px";
      }
    });

  }
}

var count = servicesTableArray.length; //всего записей... передать общее число генерируемых услуг
var cnt = 2; //сколько отображаем сначала ... для общей кнопки столько же, сколько для групповых
var cnt_page = Math.ceil(count / cnt); //кол-во страниц

//выводим список страниц
var paginator = document.querySelector(".paginator");
var page = "";
for (var i = 0; i < cnt_page; i++) {
  if(i == 0)
  {
    page += "<span data-page=" + i * cnt + "  id=\"page" + (i + 1) + "\"" + "class=\"paginator_active\"" + "\">" + (i + 1) + "</span>";
  }
  else
  page += "<span data-page=" + i * cnt + "  id=\"page" + (i + 1) + "\">" + (i + 1) + "</span>";
}

paginator.innerHTML = page;

//выводим первые записи {cnt}
var div_num = document.querySelectorAll(".services__list-container");
for (var i = 0; i < div_num.length; i++) {
  if (i < cnt) {  
    div_num[i].style.display = "block";
  }
}


var main_page = document.getElementById("service_list_id");
main_page.classList.add("paginator_active"); 



//листаем
function pagination(event) {
  var e = event || window.event;
  var target = e.target;
  var id = target.id;
  
  if (target.tagName.toLowerCase() != "span") return;
  
  var num_ = id.substr(4);
  var data_page = +target.dataset.page;
  console.log(page);
  main_page.classList.remove("paginator_active");
  main_page = document.getElementById(id);
  main_page.classList.add("paginator_active");

  var j = 0;
  for (var i = 0; i < div_num.length; i++) {
    var data_num = div_num[i].dataset.num;
    
     div_num[i].style.display = "none";

  }
  for (var i = data_page; i < div_num.length; i++) {
    if (j >= cnt) break;
    div_num[i].style.display = "block";
    j++;
  }
}



</script>


