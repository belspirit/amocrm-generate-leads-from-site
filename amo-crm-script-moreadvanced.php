    require_once ASTRA_CHILD_DIR . 'inc/wp-amocrm/amocrm.phar';


// Function for basic field validation (present and neither empty nor only white space)
    function IsNullOrEmptyString($question)
    {
        return (!isset($question) || trim($question) === '');
    }

    try {

        //Конфиг для авторизации в amoCRM
        $domain = 'yourdomain';
        $login = 'yourlogin';
        $hash = 'secretkey';

        //ID юзеров amoCRM
        $User = 654582;

        $current_user = $User;

        //Создаем новый экземпляр
        $amo = new \AmoCRM\Client($domain, $login, $hash);

        $form_data = $_POST;

        //прокидываем в переменные значения полей
        $user_name = $form_data['client-name'];
        $user_email = $form_data['email'];
        $user_phone = $form_data['phone'];
        $user_message = $form_data['message'];
        $form_type = $form_data['form-type'];

        //Калькулятор
        $calc_area     = $form_data['square']; //площадь потолка
        $calc_brand    = $form_data['proiz']; // производитель
        $calc_lights    = $form_data['lights']; // кол-во светильников
        $calc_corners = $form_data['corners']; // кол-во углов


        //Собираем UTM метки со сгенерированных полей
        function getUtmField($fieldName)
        {
            if (isset($_POST[$fieldName])) {
                return $_POST[$fieldName];
            } else {
                return '(none)';
            }
        }

        $firstTyp = getUtmField('sb_first_typ');
        $firstSrc = getUtmField('sb_first_src');
        $firstMdm = getUtmField('sb_first_mdm');
        $firstCmp = getUtmField('sb_first_cmp');
        $firstCnt = getUtmField('sb_first_cnt');
        $firstTrm = getUtmField('sb_first_trm');

        $firstAddfd = getUtmField('sb_first_add_fd');
        $firstAddep = getUtmField('sb_first_add_ep');
        $firstAddRf = getUtmField('sb_first_add_rf');

        $currentTyp = getUtmField('sb_current_typ');
        $currentSrc = getUtmField('sb_current_src');
        $currentMdm = getUtmField('sb_current_mdm');
        $currentCmp = getUtmField('sb_current_cmp');
        $currentCnt = getUtmField('sb_current_cnt');
        $currentTrm = getUtmField('sb_current_trm');

        $currentAddFd = getUtmField('sb_current_add_fd');
        $currentAddEp = getUtmField('sb_current_add_ep');
        $currentAddRf = getUtmField('sb_current_add_rf');

        $sessionPgs = getUtmField('sb_session_pgs');
        $sessionCpg = getUtmField('sb_session_cpg');

        $udataVst = getUtmField('sb_udata_vst');
        $udataUag = getUtmField('sb_udata_uag');

        //User message template
        $message = "СООБЩЕНИЕ\n" . $user_message . " \n -------------- \n";

        //Calculator data template
        $calculator = "КАЛЬКУЛЯТОР\n";
        $calculator .= "Производитель = " .  $calc_brand . "\n";
        $calculator .= "Площадь потолка = " .  $calc_area . " м.кв.\n";
        $calculator .= "Кол. светильников = " .  $calc_lights . " шт.\n";
        $calculator .= "Кол. углов = " .  $calc_corners . " шт.\n";
        $calculator .= " -------------- \n";


        // Заполняем текст сообщения
        $note_body = '';
        $note_body .= !IsNullOrEmptyString($user_message) ? $message : "";
        $note_body .= ($form_type === "Калькулятор") ? $calculator : "";
        $note_body .= "Данные по UTM меткам\n";
        $note_body .= "-------------------- \n";
        $note_body .= "ПЕРВЫЙ ВИЗИТ\n";
        $note_body .= "Тип трафика = " . $firstTyp . "\n";
        $note_body .= "utm_source = " . $firstSrc . "\n";
        $note_body .= "utm_medium = " . $firstMdm . "\n";
        $note_body .= "utm_campaign = " . $firstCmp . "\n";
        $note_body .= "utm_content = " . $firstCnt . "\n";
        $note_body .= "utm_term = " . $firstTrm . "\n";
        $note_body .= "Дата и время визита = " . $firstAddfd . "\n";
        $note_body .= "Точка входа визита = " . $firstAddep . "\n";
        $note_body .= "Реферер = " . $firstAddRf . "\n\n";
        $note_body .= " -------------------- \n";
        $note_body .= "ТЕКУЩИЙ ВИЗИТ\n";
        $note_body .= "Тип трафика = " . $currentTyp . "\n";
        $note_body .= "utm_source = " . $currentSrc . "\n";
        $note_body .= "utm_medium = " . $currentMdm . "\n";
        $note_body .= "utm_campaign = " . $currentCmp . "\n";
        $note_body .= "utm_content = " . $currentCnt . "\n";
        $note_body .= "utm_term = " . $currentTrm . "\n";
        $note_body .= "Дата и время визита = " . $currentAddFd . "\n";
        $note_body .= "Точка входа визита = " . $currentAddEp . "\n";
        $note_body .= "Реферер = " . $currentAddRf . "\n";
        $note_body .= "--------------------\n";
        $note_body .= "ДАННЫЕ О ТЕКУЩЕЙ СЕССИИ\n";
        $note_body .= "Сколько страниц сайта посмотрел посетитель = " . $sessionPgs . "\n";
        $note_body .= "URL текущей страницы посетителя = " . $sessionCpg . "\n";
        $note_body .= " -------------------- \n";
        $note_body .= "ЛИЧНЫЕ ДАННЫЕ\n";
        $note_body .= "Сколько раз пользователь посещал сайт = " . $udataVst . "\n";
        $note_body .= "Текущий браузер = " . $udataUag;


        // Добавление сделки
        $lead = $amo->lead;// Создаем лида
        $lead['name'] = 'Заявка с сайта houze.by';  //название сделки
        $lead['responsible_user_id'] = $current_user; // ID ответственного
        //$lead['price'] = 0;  // В поле "Бюджет"
        $lead['pipeline_id'] = 51612; // ID воронки = Натяжнеые потолки
        //$lead['status_id'] = -1; // Неразобранное
        $lead['tags'] = ['houze.by', $form_type];//Теги


        //Радиобаттоны с различными источниками рекламы
        if ($currentSrc === 'google') {
            $lead->addCustomField(934397, [ // ID  поля со значением Реклама
                [2252005] // ID нужного радиобаттона
            ]);
        } else if ($currentSrc === 'yandex') {
            $lead->addCustomField(934397, [
                [2294293]
            ]);
        } else if ($currentSrc === 'mytarget') {
            $lead->addCustomField(934397, [
                [2269343]
            ]);
        } else {
            $lead->addCustomField(934397, [
                [2257565]
            ]);
        }


        // КАЛЬКУЛЯТОР
//    $lead->addCustomField(89025, [[$calc_area]]);
//    $lead->addCustomField(481679, [[$calc_brand]]);
//    $lead->addCustomField(481681, [[$calc_lights]]);
//    $lead->addCustomField(481683, [[$calc_corners]]);

        $id = $lead->apiAdd();  // добавляем сделку и получаем ее ID


        $contact = $amo->contact;// Получение экземпляра модели для работы с контактами
        // Заполнение полей модели
        $contact['name'] = !IsNullOrEmptyString($user_name) ? $user_name : 'Имя не указано';
        $contact['linked_leads_id'] = [(int)$id]; //привязываем к лиду по его ID

        $contact->addCustomField(789320, [ // ID  поля в которое будт приходить заявки
            [$user_email, 'PRIV']// Имейл контакта
        ]);
        $contact->addCustomField(789318, [ // ID  поля в которое будт приходить заявки
            [$user_phone, 'WORK']// Номер телефона контакта
        ]);
//    $contact->addCustomField(478049, [ // ID  поля в которое будт приходить заявки
//        [928325]// Источник заявки
//    ]);

        $contact_id = $contact->apiAdd();// Добавление нового контакта и получение его ID


        $note = $amo->note;//Добавление примечания
        $note['element_id'] = $id; //привязываем к лиду по его ID
        $note['element_type'] = \AmoCRM\Models\Note::TYPE_LEAD; // 1 - contact, 2 - lead
        $note['note_type'] = \AmoCRM\Models\Note::COMMON; // @see https://developers.amocrm.ru/rest_api/notes_type.php
        $note['text'] = $note_body; //В переменной $note_body - текст примечания (Сообщение + UTM метки)
        $note_id = $note->apiAdd();


    } catch (\AmoCRM\Exception $e) {
        printf('Error (%d): %s', $e->getCode(), $e->getMessage());
    }
