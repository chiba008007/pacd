<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FormDataAttendee extends Model
{
    use HasFactory;

    protected $fillable = [
        'attendee_id',
        'form_input_value_id',
        'data',
        'data_sub',
    ];

    /**
     * フォーム入力データからデータを整形してデータベースに登録する
     *
     * @param array $input
     * @param Attendee $attendee
     * @return void
     */
    static public function createFromInputData(array $data, Attendee $attendee)
    {
        try {
            foreach ($data as $input_id => $input) {
                if (isset($input['data'])) {
                    foreach ($input['data'] as $value_id => $val) {
                        $data = [
                            'attendee_id' => $attendee->id,
                            'form_input_value_id' => $value_id,
                            'data' => $val ?? '',
                        ];
                        // プルダウン型の場合、valにvalue_idが入っているためデータを整形
                        if ($input['type'] == config('pacd.form.input_type.select')) {
                            $data['form_input_value_id'] = $val;
                            $data['data'] = FormInputValue::find($val)->value;
                        }
                        // テキストボックス付き複数選択型のテキストデータを追加
                        if (isset($input['data_sub'][$value_id])) {
                            $data['data_sub'] = $input['data_sub'][$value_id] ?? '';
                        }
                        self::create($data);
                    }
                }
            }
        } catch (\Exception $e) {
            return $e;
        }
    }

    /**
     * フォーム入力データからデータを整形してデータベースを更新する
     *
     * @param array $input
     * @param Attendee $attendee
     * @return void
     */
    static public function updateFromInputData(array $data, Attendee $attendee)
    {
        try {
            $custom_data = $attendee->custom_form_data->keyBy('form_input_value_id');
            foreach ($data as $input_id => $input) {
                if (isset($input['data'])) {
                    foreach ($input['data'] as $value_id => $val) {
                        if (isset($custom_data[$value_id])) {       // 更新
                            $custom_data[$value_id]->data = $val;
                            if (isset($input['data_sub']) && isset($input['data_sub'][$value_id])) {
                                $custom_data[$value_id]->data_sub = $input['data_sub'][$value_id] ?? '';
                            }
                            $custom_data[$value_id]->save();
                            unset($custom_data[$value_id]);
                        } else {                                    // 追加
                            $data = [
                                'attendee_id' => $attendee->id,
                                'form_input_value_id' => $value_id,
                                'data' => $val ?? '',
                            ];
                            // プルダウン型の場合、valにvalue_idが入っているためデータを整形
                            if ($input['type'] == config('pacd.form.input_type.select')) {
                                $data['form_input_value_id'] = $val;
                                $data['data'] = FormInputValue::find($val)->value;
                            }
                            // テキストボックス付き複数選択型のテキストデータを追加
                            if (isset($input['data_sub'][$value_id])) {
                                $data['data_sub'] = $input['data_sub'][$value_id] ?? '';
                            }
                            self::create($data);
                        }
                    }
                }
            }
            // 不要になったデータを削除
            if ($custom_data->count()) {
                foreach ($custom_data as $data) {
                    $data->delete();
                };
            }
        } catch (\Exception $e) {
            return $e;
        }
    }
}
