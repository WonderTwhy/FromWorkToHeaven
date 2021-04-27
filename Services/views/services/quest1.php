<?
use app\models\Services;
use app\models\Services_groups;
use yii\helpers\Html;
?>   
  


<!-- Создать кнопку, которая будет выводить все услуги независимо от id -->
<!-- <div class="paginator" onclick="pagination(event)"></div> -->
<!-- Разместить блок 'paginator' снизу фрейма -->

<link rel='stylesheet' href='/web/css/Services.css'>


<div>
  <div id="mainContainer" class="services">
    <div id="groupList" class="services__groups-container">
      <div id="serviceSearch" class="services__search-container">
        <input id="searchInput" type="text" class="services__search-input">
        <img src="/web/img/search.png" alt="" class = 'icon'>
      </div>
      <ul class="services__list">
      <li class='services__list-container all'>
        <button id="services_button_all" class='services__list-button all' onclick="getServices('all')">Все услуги</button>  
        <div id="services_list_all" class="services__list-cont"></div>
      </li>
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
    
    <div class="services__result-container">
          <div id="servicesContainer">
          
          </div>
          <div id="paginationContainer" class="pagination">

          </div>
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
const paginationContainer = document.getElementById("paginationContainer");
const searchInput = document.getElementById("searchInput");
const groupButtons = document.querySelectorAll(".services__list-button:not(.all)");
const groupList = document.getElementById("groupList");
const serviceSearch = document.getElementById("serviceSearch");

const maxElementHeight = 50;
const collapseWidth = 991;
const maxServiceItems = 2;
var mobileScreen = false;


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
  if (paginationContainer) paginationContainer.innerHTML = "";

  if (buttonsContainers) {
    buttonsContainers.forEach(function(elem, key) {elem.innerHTML = "";});
  }
}

////////////////////////////////////pag
function getItemsList(array, pageSize) {
    const pageNum = Math.ceil(array.length / pageSize);
    const listPageObjects = new Array();

    let elemNum = 0,
        elemPage = 0;

    if (!Array.isArray(array)) array = Object.entries(array);
    array.forEach(function(value, key) {
        if (listPageObjects[elemPage] == undefined) listPageObjects[elemPage] = new Array();
        listPageObjects[elemPage][elemNum] = value;

        elemNum++;

        if (elemNum >= pageSize) {
            elemNum = 0;
            elemPage++;
        }
    });

    return listPageObjects;
}

function getButtonsList(array, curPage, maxItems = 4) {
  let leftArray = new Array();
  let rightArray = new Array();
  for (let i = curPage - 1, it = 0; i >= 0 && it < maxItems; i--, it++) {
      if (array[i]) {
          if (it == maxItems - 2) {
              leftArray.unshift('...');
          } else if (it == maxItems - 1 && i != 0) {
              leftArray.unshift({
                  pageNumber: 0,
                  itemsList: array[0]
              })
          } else {
              leftArray.unshift({
                  pageNumber: i,
                  itemsList: array[i]
              })
          }
      }
  }

  for (let i = curPage + 1, it = 0; i < array.length && it < maxItems; i++, it++) {
      if (array[i]) {
          if (it == maxItems - 2) {
              rightArray.push('...');
          } else if (it == maxItems - 1 && i != 0) {
              rightArray.push({
                  pageNumber: array.length - 1,
                  itemsList: array[array.length - 1]
              })
          } else {
              rightArray.push({
                  pageNumber: i,
                  itemsList: array[i]
              })
          }
      }
  }

  const curItem = {
      pageNumber: curPage,
      itemsList: array[curPage],
      active: true
  }
  let resultButtonsList = new Array();
  resultButtonsList = resultButtonsList.concat(leftArray, curItem, rightArray);

  if (array.length > 1) {
    resultButtonsList.unshift('Prev');
    resultButtonsList.push('Next');
  } 

  return resultButtonsList;
}

function createGroupButtons(arrayButtons, arrayItems, parent, parentContent) {
  if (parent) parent.innerHTML = "";
  else return;

  function newPageValue(curPage, value, arrayItems) {
      if (typeof value !== 'object') {
          if (value.toLowerCase() == "next" && curPage + 1 <= arrayItems.length - 1) {
              return curPage + 1;
          } else if (value.toLowerCase() == "prev" && curPage - 1 >= 0) {
              return curPage - 1;
          } else return false;
      } else {
          return value.pageNumber != curPage ? value.pageNumber : false;
      }
  }

  let curPage = null;
  arrayButtons.forEach(function(value, key) {
      let paginationItem;

      if (value == "...") {
          paginationItem = document.createElement('div');
          paginationItem.classList.add('clear');
      } else {
          paginationItem = document.createElement('a');
      }

      paginationItem.innerText = typeof value === 'object' ? value.pageNumber + 1 : value;
      paginationItem.setAttribute('data-page', typeof value === 'object' ? value.pageNumber : value);

      if (value.active && value.active == true) {
          paginationItem.classList.add('active')
          curPage = value;
      }

      paginationItem.classList.add('pagination__item');
      parent.append(paginationItem);

      if (value != "...") {
          paginationItem.addEventListener('click', () => {
              const newPage = newPageValue(curPage.pageNumber, value, arrayItems);
              if (newPage !== false) {
                  const bList = getButtonsList(arrayItems, newPage);
                  createGroupButtons(bList, arrayItems, parent, parentContent);
              }
          });
      }
  });

  if (curPage && curPage.itemsList && parentContent) {
    parentContent.innerHTML = "";

    curPage.itemsList.forEach(function(value, key) {
      createServiceElement(parentContent, value);
    });

  }

}
///////////////////////////////////////////////////pag

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
      const servicesItemsList = new Array();
      const parent = mobileScreen && list ? list : servicesContainer;

      servicesTableArray.forEach(function(value, key) {
        if (id == "all" || value["idServiceGroup"] == serviceGroupId) {
          servicesItemsList.push(value);
        }
      });

      if (servicesItemsList.length > 0) {
        const iList = getItemsList(servicesItemsList, maxServiceItems);
        const bList = getButtonsList(iList, 0);

        createGroupButtons(bList, iList, paginationContainer, parent);
      }
    }

    checkEmptyServicesContainer();
  }

}

// -----------------------------

clearAllServices();
checkTypeScreen();
checkEmptyServicesContainer();



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

</script>


