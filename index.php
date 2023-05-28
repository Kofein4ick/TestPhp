<!DOCTYPE html>
<html lang='ru'>

<head>
    <meta charset='utf-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1, shrink-to-fit=no'>
    <link rel="stylesheet" href="style.css" />
    <title>Таблица продуктов</title>
</head>

<body>

<div id="mainContainer"> <!-- Основной контейнер -->
    <h1>Таблица продуктов</h1>
    <p>Фильтрация по:
        <!-- Блок фильтрации -->
        <span>
            <input id="input_name" type="text" size=10 value=''></input>
            <button id="button_name" onclick="filterHandler()">Имени</button>
        </span>
        <!-- Блок сброса -->
        <span><button id="button_filt" onclick="remFilterHandler()">Сброс</button></span>
    </p>
    <!-- Таблица -->
    <table id="table" border="1" cellpadding="4" cellspacing="0" align="center">
    <thead>
    <tr>
        <th  onclick="sortHandler(event)">Id </th>
        <th class='th_name' onclick="sortHandler(event)">Имя </th>
        <th class='th_price' onclick="sortHandler(event)">Стоимость </th>
    </tr>
    </thead>
    <tbody>
    <!-- Обработчик содержимого таблицы -->
    <?php
            $conn = new mysqli("localhost", "root", "", "Test_db");//Подключение к БД
            $sql = "SELECT * FROM Products"; // Запрос на выборку
            if($conn->connect_error){//Проверка подключения
                die("Ошибка: " . $conn->connect_error);
            }
            $result = $conn->query($sql);//Выполнение запроса
            if($result){//Получение данных из запроса и формирование содержимого таблицы
                foreach($result as $row){
                    echo "<tr>"."<td>".$row["id"]."</td>";
                    echo "<td class='th_name'>".$row["name"]."</td>";
                    echo "<td class='th_price'>".$row["price"]."</td>"."</tr>";
                }
            }
            $conn->close();//закрытие соединения
    ?>
    </tbody>
    </table>
    <!-- Скрипт с определением обработчиков -->
    <script>
        let startTable=Array.from(table.rows).slice(1);//Формиирование массива из содержимого таблицы
        //Функция обработчик сортировки
        function sortHandler({target}){//Работаем с объектом события
            let order=(target.dataset.order=-(target.dataset.order || -1));//Определяем направление сортировки
            let index = [...target.parentNode.cells].indexOf(target);//Получаем индекс выбранной ячейки
            //Делаем копию массива таблицы и сортируем ее
            //Сравниваются строки, сравнение идет посимвольно
            let sortedRows = Array.from(table.rows).slice(1)
            .sort((rowA, rowB) => ""+rowA.cells[index].innerHTML > ""+rowB.cells[index].innerHTML ? order : -order);
            table.tBodies[0].append(...sortedRows);//Перестановка строк таблицы в соответствии с сортировкой
            for(const cell of target.parentNode.cells)//Указываем ячейке заголовка класс для отрисовки стрелки направления
                cell.classList.toggle('sorted', cell === target);
            return;
        };
        //Функция обработчик фильтрации
        function filterHandler(){
            let str=input_name.value;//Получение значения текстового поля
            for(let i=0;i<table.tHead.rows[0].cells.length;i++){//Удаляем класс в ячейке заголовка
                    table.tHead.rows[0].cells[i].classList.remove('sorted');
            }
            table.tBodies[0].append(...startTable); //Фильтрация производится относительно исходной таблицы
            let reg= new RegExp(str)//Определяем регулярное выражение
            //Массив отфильтрованных строк
            let filterRows = Array.from(table.rows).slice(1)
            .filter(row=>{
                if(reg.test(""+row.cells[1].innerHTML))//Проверка совпадения строки с регулярным выражением
                    return row.cells[1].innerHTML;
            });
            let len=table.tBodies[0].rows.length;
            for(let i=0;i<len;i++){//Удаление из таблицы всех строк(кроме заголовка)
                table.tBodies[0].rows[0].remove();
            };
            table.tBodies[0].append(...filterRows);//Вставка отфильтрованных строк
            return;
        };
        //Функция обработчик кнопки сброса
        function remFilterHandler(){
                input_name.value='';
                table.tBodies[0].append(...startTable);//Восстанавливаем исходную таблицу
                for(let i=0;i<table.tHead.rows[0].cells.length;i++){//Удаляем класс в ячейке заголовка
                    table.tHead.rows[0].cells[i].classList.remove('sorted');
                }
                return;
        };
    </script>
</div>
</body>

</html>