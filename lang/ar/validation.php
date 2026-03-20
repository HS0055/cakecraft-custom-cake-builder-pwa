<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default error messages used by
    | the validator class. Some of these rules have multiple versions such
    | as the size rules. Feel free to tweak each of these messages here.
    |
    */

    'accepted' => 'يجب قبول الحقل :attribute.',
    'accepted_if' => 'يجب قبول الحقل :attribute عندما يكون الحقل :other قيمته :value.',
    'active_url' => 'الحقل :attribute ليس عنوان URL صالحًا.',
    'after' => 'يجب أن يكون الحقل :attribute تاريخًا بعد :date.',
    'after_or_equal' => 'يجب أن يكون الحقل :attribute تاريخًا يوافق أو بعد :date.',
    'alpha' => 'يجب ألا يحتوي الحقل :attribute إلا على حروف.',
    'alpha_dash' => 'يجب ألا يحتوي الحقل :attribute إلا على حروف، وأرقام، وشرطات، وشرطات سفلية.',
    'alpha_num' => 'يجب أن يحتوي الحقل :attribute على حروف وأرقام فقط.',
    'any_of' => 'الحقل :attribute غير صالح.',
    'array' => 'يجب أن يكون الحقل :attribute مصفوفة.',
    'ascii' => 'يجب أن يحتوي الحقل :attribute على رموز وأرقام وحروف أحادية البايت فقط بصيغة ASCII.',
    'before' => 'يجب أن يكون الحقل :attribute تاريخًا قبل :date.',
    'before_or_equal' => 'يجب أن يكون الحقل :attribute تاريخًا يوافق أو قبل :date.',
    'between' => [
        'array' => 'يجب أن يحتوي الحقل :attribute على ما بين :min و :max من العناصر.',
        'file' => 'يجب أن يكون حجم الحقل :attribute بين :min و :max كيلوبايت.',
        'numeric' => 'يجب أن تكون قيمة الحقل :attribute بين :min و :max.',
        'string' => 'يجب أن يكون طول نص الحقل :attribute بين :min و :max من الحروف.',
    ],
    'boolean' => 'يجب أن تكون قيمة الحقل :attribute إما true أو false.',
    'can' => 'الحقل :attribute يحتوي على قيمة غير مصرح بها.',
    'confirmed' => 'حقل التأكيد :attribute غير متطابق.',
    'contains' => 'الحقل :attribute يفتقد إلى قيمة مطلوبة.',
    'current_password' => 'كلمة المرور غير صحيحة.',
    'date' => 'الحقل :attribute ليس تاريخًا صالحًا.',
    'date_equals' => 'يجب أن يكون الحقل :attribute تاريخًا مطابقًا لـ :date.',
    'date_format' => 'يجب أن يتوافق الحقل :attribute مع التنسيق :format.',
    'decimal' => 'يجب أن يحتوي الحقل :attribute على :decimal خانة/خانات عشرية.',
    'declined' => 'يجب رفض الحقل :attribute.',
    'declined_if' => 'يجب رفض الحقل :attribute عندما يكون الحقل :other قيمته :value.',
    'different' => 'يجب أن يكون الحقلان :attribute و :other مختلفين.',
    'digits' => 'يجب أن يتكون الحقل :attribute من :digits رقمًا/أرقام.',
    'digits_between' => 'يجب أن يكون الحقل :attribute بين :min و :max من الأرقام.',
    'dimensions' => 'الحقل :attribute يحتوي على أبعاد صورة غير صالحة.',
    'distinct' => 'للحقل :attribute قيمة مكررة.',
    'doesnt_contain' => 'يجب ألا يحتوي الحقل :attribute على أي من القيم التالية: :values.',
    'doesnt_end_with' => 'يجب ألا ينتهي الحقل :attribute بأي من القيم التالية: :values.',
    'doesnt_start_with' => 'يجب ألا يبدأ الحقل :attribute بأي من القيم التالية: :values.',
    'email' => 'يجب أن يكون الحقل :attribute عنوان بريد إلكتروني صالحًا.',
    'encoding' => 'يجب أن يكون الحقل :attribute مشفرًا بتنسيق :encoding.',
    'ends_with' => 'يجب أن ينتهي الحقل :attribute بأحد القيم التالية: :values.',
    'enum' => 'الحقل :attribute المختار غير صالح.',
    'exists' => 'الحقل :attribute المختار غير صالح.',
    'extensions' => 'يجب أن يكون الحقل :attribute بتمديد من القائمة التالية: :values.',
    'file' => 'يجب أن يكون الحقل :attribute ملفًا.',
    'filled' => 'يجب أن يحتوي الحقل :attribute على قيمة.',
    'gt' => [
        'array' => 'يجب أن يحتوي الحقل :attribute على أكثر من :value من العناصر.',
        'file' => 'يجب أن يكون حجم الحقل :attribute أكبر من :value كيلوبايت.',
        'numeric' => 'يجب أن تكون قيمة الحقل :attribute أكبر من :value.',
        'string' => 'يجب أن يكون طول نص الحقل :attribute أكبر من :value من الحروف.',
    ],
    'gte' => [
        'array' => 'يجب أن يحتوي الحقل :attribute على :value من العناصر أو أكثر.',
        'file' => 'يجب أن يكون حجم الحقل :attribute أكبر من أو يساوي :value كيلوبايت.',
        'numeric' => 'يجب أن تكون قيمة الحقل :attribute أكبر من أو يساوي :value.',
        'string' => 'يجب أن يكون طول نص الحقل :attribute أكبر من أو يساوي :value من الحروف.',
    ],
    'hex_color' => 'يجب أن يكون الحقل :attribute لونًا سداسي عشريًا صالحًا.',
    'image' => 'يجب أن يكون الحقل :attribute صورة.',
    'in' => 'الحقل :attribute المختار غير صالح.',
    'in_array' => 'يجب أن يكون الحقل :attribute موجودًا في :other.',
    'in_array_keys' => 'يجب أن يحتوي الحقل :attribute على مفتاح واحد على الأقل من القائمة التالية: :values.',
    'integer' => 'يجب أن يكون الحقل :attribute عددًا صحيحًا.',
    'ip' => 'يجب أن يكون الحقل :attribute عنوان IP صالحًا.',
    'ipv4' => 'يجب أن يكون الحقل :attribute عنوان IPv4 صالحًا.',
    'ipv6' => 'يجب أن يكون الحقل :attribute عنوان IPv6 صالحًا.',
    'json' => 'يجب أن يكون الحقل :attribute نصًا من نوع JSON صالحًا.',
    'list' => 'يجب أن يكون الحقل :attribute قائمة.',
    'lowercase' => 'يجب أن يكون الحقل :attribute مكتوبًا بحروف صغيرة (Lowercase).',
    'lt' => [
        'array' => 'يجب أن يحتوي الحقل :attribute على أقل من :value من العناصر.',
        'file' => 'يجب أن يكون حجم الحقل :attribute أقل من :value كيلوبايت.',
        'numeric' => 'يجب أن تكون قيمة الحقل :attribute أقل من :value.',
        'string' => 'يجب أن يكون طول نص الحقل :attribute أقل من :value من الحروف.',
    ],
    'lte' => [
        'array' => 'يجب ألا يحتوي الحقل :attribute على أكثر من :value من العناصر.',
        'file' => 'يجب أن يكون حجم الحقل :attribute أقل من أو يساوي :value كيلوبايت.',
        'numeric' => 'يجب أن تكون قيمة الحقل :attribute أقل من أو يساوي :value.',
        'string' => 'يجب أن يكون طول نص الحقل :attribute أقل من أو يساوي :value من الحروف.',
    ],
    'mac_address' => 'يجب أن يكون الحقل :attribute عنوان MAC صالحًا.',
    'max' => [
        'array' => 'يجب ألا يحتوي الحقل :attribute على أكثر من :max من العناصر.',
        'file' => 'يجب ألا يكون حجم الحقل :attribute أكبر من :max كيلوبايت.',
        'numeric' => 'يجب ألا تكون قيمة الحقل :attribute أكبر من :max.',
        'string' => 'يجب ألا يكون طول نص الحقل :attribute أكبر من :max من الحروف.',
    ],
    'max_digits' => 'يجب ألا يحتوي الحقل :attribute على أكثر من :max أرقام.',
    'mimes' => 'يجب أن يكون الحقل :attribute ملفًا من نوع: :values.',
    'mimetypes' => 'يجب أن يكون الحقل :attribute ملفًا من نوع: :values.',
    'min' => [
        'array' => 'يجب أن يحتوي الحقل :attribute على الأقل على :min من العناصر.',
        'file' => 'يجب أن يكون حجم الحقل :attribute على الأقل :min كيلوبايت.',
        'numeric' => 'يجب أن تكون قيمة الحقل :attribute على الأقل :min.',
        'string' => 'يجب أن يكون طول نص الحقل :attribute على الأقل :min من الحروف.',
    ],
    'min_digits' => 'يجب أن يحتوي الحقل :attribute على الأقل على :min أرقام.',
    'missing' => 'يجب أن يكون الحقل :attribute مفقودًا.',
    'missing_if' => 'يجب أن يكون الحقل :attribute مفقودًا عندما يكون الحقل :other قيمته :value.',
    'missing_unless' => 'يجب أن يكون الحقل :attribute مفقودًا ما لم يكن الحقل :other قيمته :value.',
    'missing_with' => 'يجب أن يكون الحقل :attribute مفقودًا عند توفر الحقل :values.',
    'missing_with_all' => 'يجب أن يكون الحقل :attribute مفقودًا عند توفر الحقول :values.',
    'multiple_of' => 'يجب أن يكون الحقل :attribute مضعفًا للقيمة :value.',
    'not_in' => 'الحقل :attribute المختار غير صالح.',
    'not_regex' => 'تنسيق الحقل :attribute غير صالح.',
    'numeric' => 'يجب أن يكون الحقل :attribute رقمًا.',
    'password' => [
        'letters' => 'يجب أن يحتوي الحقل :attribute على حرف واحد على الأقل.',
        'mixed' => 'يجب أن يحتوي الحقل :attribute على حرف واحد كبير على الأقل وحرف واحد صغير.',
        'numbers' => 'يجب أن يحتوي الحقل :attribute على رقم واحد على الأقل.',
        'symbols' => 'يجب أن يحتوي الحقل :attribute على رمز واحد على الأقل.',
        'uncompromised' => 'الحقل :attribute الذي تم إدخاله ظهر في تسريبات بيانات. يرجى اختيار :attribute آخر.',
    ],
    'present' => 'يجب تقديم الحقل :attribute.',
    'present_if' => 'يجب تقديم الحقل :attribute عندما يكون الحقل :other قيمته :value.',
    'present_unless' => 'يجب تقديم الحقل :attribute ما لم يكن الحقل :other قيمته :value.',
    'present_with' => 'يجب تقديم الحقل :attribute عند توفر الحقل :values.',
    'present_with_all' => 'يجب تقديم الحقل :attribute عند توفر الحقول :values.',
    'prohibited' => 'الحقل :attribute محظور.',
    'prohibited_if' => 'الحقل :attribute محظور عندما يكون الحقل :other قيمته :value.',
    'prohibited_if_accepted' => 'الحقل :attribute محظور عندما يتم قبول الحقل :other.',
    'prohibited_if_declined' => 'الحقل :attribute محظور عندما يتم رفض الحقل :other.',
    'prohibited_unless' => 'الحقل :attribute محظور ما لم يكن الحقل :other موجودًا في القائمة :values.',
    'prohibits' => 'الحقل :attribute يمنع الحقل :other من التواجد.',
    'regex' => 'تنسيق الحقل :attribute غير صالح.',
    'required' => 'حقل :attribute مطلوب.',
    'required_array_keys' => 'يجب أن يحتوي الحقل :attribute على مدخلات لـ: :values.',
    'required_if' => 'حقل :attribute مطلوب عندما يكون الحقل :other قيمته :value.',
    'required_if_accepted' => 'حقل :attribute مطلوب عندما يتم قبول الحقل :other.',
    'required_if_declined' => 'حقل :attribute مطلوب عندما يتم رفض الحقل :other.',
    'required_unless' => 'حقل :attribute مطلوب ما لم يكن الحقل :other موجودًا في القائمة :values.',
    'required_with' => 'حقل :attribute مطلوب عند توفر الحقل :values.',
    'required_with_all' => 'حقل :attribute مطلوب عند توفر الحقول :values.',
    'required_without' => 'حقل :attribute مطلوب عند عدم توفر الحقل :values.',
    'required_without_all' => 'حقل :attribute مطلوب عند عدم توفر أي من الحقول :values.',
    'same' => 'يجب أن يتطابق الحقلان :attribute و :other.',
    'size' => [
        'array' => 'يجب أن يحتوي الحقل :attribute على :size من العناصر.',
        'file' => 'يجب أن يكون حجم الحقل :attribute :size كيلوبايت.',
        'numeric' => 'يجب أن تكون قيمة الحقل :attribute مساوية لـ :size.',
        'string' => 'يجب أن يتكون نص الحقل :attribute من :size من الحروف.',
    ],
    'starts_with' => 'يجب أن يبدأ الحقل :attribute بأحد القيم التالية: :values.',
    'string' => 'يجب أن يكون الحقل :attribute نصًا.',
    'timezone' => 'يجب أن يكون الحقل :attribute منطقة زمنية صالحة.',
    'unique' => 'قيمة :attribute مستخدمة من قبل.',
    'uploaded' => 'فشل تحميل الحقل :attribute.',
    'uppercase' => 'يجب أن يكون الحقل :attribute مكتوبًا بحروف كبيرة (Uppercase).',
    'url' => 'يجب أن يكون الحقل :attribute عنوان URL صالحًا.',
    'ulid' => 'يجب أن يكون الحقل :attribute معرف ULID صالحًا.',
    'uuid' => 'يجب أن يكون الحقل :attribute معرف UUID صالحًا.',

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using the
    | convention "attribute.rule" to name the lines. This makes it quick to
    | specify a specific custom language line for a given attribute rule.
    |
    */

    'custom' => [
        'attribute-name' => [
            'rule-name' => 'custom-message',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to swap our attribute placeholder
    | with something more reader friendly such as "E-Mail Address" instead
    | of "email". This simply helps us make our message more expressive.
    |
    */

    'attributes' => [],

];
