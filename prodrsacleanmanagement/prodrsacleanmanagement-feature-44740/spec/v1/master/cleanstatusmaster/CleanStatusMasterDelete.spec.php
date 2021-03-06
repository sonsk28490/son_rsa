<?php

/**
 * Created by TAP Co.,Ltd .
 * User: y_kishimoto
 * Date: 2020/03/04
 */

use Pimple\Container;
use App\Http\Library\cleanstatusmaster\CleanStatusMasterContainer;
use App\Http\Requests\cleanstatusmaster\CleanStatusMasterDeleteRequest;
use Modules\params\Params;
use Modules\responses\JsonResponse;
use Modules\responses\ResponseConfig;
use App\Models\MasterRsCleanStatusModel;

/**
 * Unit Test
 * CleanStatusMasterController Delete API
 *
 * @author y_kishimoto
 */
describe('[ CleanStatusMasterController Delete API ]', function () {

    beforeAll(function () {
        $this->data = [
            "delete_ids" => [],
            "delete_type" => 1
        ];

        $this->storeData = [
            "systemusercompanyid" => "C01",
            "facilityid" => "001",
            "cleaningstatuscode" => "TCD",
            "cleaningstatusname" => "UNIT.TEST",
            "description" => 'UNIT.TESTデータ',
            "sequence" => 3,
        ];
    });

    beforeEach(function () {
        $app = require 'bootstrap/app.php';
        $app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
    });

    given('url', function () {
        return  'api/v1/master/clean/statuses';
    });

    given('storeRequest', function () {
        $model = new MasterRsCleanStatusModel();
        $response = $model->insert($this->storeData);

        return json_decode($response, true) ?? null;
    });

    given('storeRequestV2', function () {
        $model = new MasterRsCleanStatusModel();
        $form = $this->storeData;
        $form['cleaningstatuscode'] = 'TCD2';
        $form['cleaningstatusname'] = 'UNIT.TEST2';
        $form['description'] = 'UNIT.TESTデータ2';
        $response = $model->insert($form);

        return json_decode($response, true) ?? null;
    });

    given('getStoredId', function () {
        return $this->storeRequest;
    });

    given('getStoredIdV2', function () {
        return $this->storeRequestV2;
    });

    context('[ Response Test ]', function () {

        // 疎通確認 テスト
        describe('Communication Confirmation Test', function () {

            // [Communication Confirmation Test 1] HTTP ステータスコード 200
            it('[Test No. ] Check If The HTTP Status Code Is 200', function () {
                $expected = 200;
                $inspection = $this->laravel->delete($this->url, array(array()));
                expect($inspection)->toPassStatus($expected);
            });
        });

        // リスポンス構造 テスト
        describe('Response Structure Test', function () {

            // [Response Structure Test 1] 成功
            it('[Test No. ] Check If Response Structure Is Success', function () {
                $storedId = $this->getStoredId;
                $form = $this->data;
                $form['delete_ids'] = [$storedId];
                $response = $this->laravel->delete($this->url, $form);
                $inspection = json_decode($response->getContent(), true);

                expect($inspection['status'])->toBe(JsonResponse::STATUS_OK);
                expect($inspection['result']['message'])->toBe(ResponseConfig::MESSAGES['SUCCESS']);
                expect($inspection['result']['code'])->toBe(ResponseConfig::CODES['SUCCESS']);
                expect($inspection['result']['data'])->not->toBeNull();
                expect($inspection['result']['errors'])->toBe([]);

                // cleanup
                $form['delete_type'] = 2;
                $this->laravel->delete($this->url, $form);
            });

            // [Response Structure Test 2] 失敗
            it('[Test No. ] Check If Response Structure Is Failed', function () {
                $response = $this->laravel->delete($this->url, array(array()));
                $inspection = json_decode($response->getContent(), true);

                expect($inspection['status'])->toBe(JsonResponse::STATUS_NG);
                expect($inspection['result']['message'])->not->toBe(ResponseConfig::MESSAGES['SUCCESS']);
                expect($inspection['result']['code'])->not->toBe(ResponseConfig::CODES['SUCCESS']);
                expect($inspection['result']['data'])->toBe(array());
                expect($inspection['result']['errors'])->not->toBeNull();
            });
        });
    });

    context('[ Request Test ]', function () {

        // 条件分岐 テスト
        describe('Conditional Branch Test', function () {

            // [Conditional Branch Test 1] 条件 論理削除
            it('[Test No. ] Check For Logical Deletion', function () {
                $storedId = $this->getStoredId;
                $form = $this->data;
                $form['delete_ids'] = [$storedId];
                $form['delete_type'] = 1;
                $response = $this->laravel->delete($this->url, $form);
                $inspection = json_decode($response->getContent(), true);

                expect($inspection['status'])->toBe(JsonResponse::STATUS_OK);
                expect($inspection['result']['message'])->toBe(ResponseConfig::MESSAGES['SUCCESS']);
                expect($inspection['result']['code'])->toBe(ResponseConfig::CODES['SUCCESS']);
                expect($inspection['result']['data'])->not->toBeNull();
                expect($inspection['result']['errors'])->toBe([]);

                // cleanup
                $form['delete_type'] = 2;
                $this->laravel->delete($this->url, $form);
            });

            // [Conditional Branch Test 2]  条件 物理削除
            it('[Test No. ] Check For physical Deletion', function () {
                $storedId = $this->getStoredId;
                $form = $this->data;
                $form['delete_ids'] = [$storedId];
                $form['delete_type'] = 2;
                $response = $this->laravel->delete($this->url, $form);
                $inspection = json_decode($response->getContent(), true);

                expect($inspection['status'])->toBe(JsonResponse::STATUS_OK);
                expect($inspection['result']['message'])->toBe(ResponseConfig::MESSAGES['SUCCESS']);
                expect($inspection['result']['code'])->toBe(ResponseConfig::CODES['SUCCESS']);
                expect($inspection['result']['data'])->not->toBeNull();
                expect($inspection['result']['errors'])->toBe([]);
            });

            // [Conditional Branch Test 3] 条件 論理削除 複数
            it('[Test No. ] Check For Logical Deletion', function () {
                $storedId1 = $this->getStoredId;
                $storedId2 = $this->getStoredIdV2;
                $form = $this->data;
                $form['delete_ids'] = [$storedId1, $storedId2];
                $form['delete_type'] = 1;
                $response = $this->laravel->delete($this->url, $form);
                $inspection = json_decode($response->getContent(), true);

                expect($inspection['status'])->toBe(JsonResponse::STATUS_OK);
                expect($inspection['result']['message'])->toBe(ResponseConfig::MESSAGES['SUCCESS']);
                expect($inspection['result']['code'])->toBe(ResponseConfig::CODES['SUCCESS']);
                expect($inspection['result']['data'])->not->toBeNull();
                expect($inspection['result']['errors'])->toBe([]);

                // cleanup
                $form['delete_type'] = 2;
                $this->laravel->delete($this->url, $form);
            });

            // [Conditional Branch Test 4]  条件 物理削除 複数
            it('[Test No. ] Check For Logical Deletion', function () {
                $storedId1 = $this->getStoredId;
                $storedId2 = $this->getStoredIdV2;
                $form = $this->data;
                $form['delete_ids'] = [$storedId1, $storedId2];
                $form['delete_type'] = 2;
                $response = $this->laravel->delete($this->url, $form);
                $inspection = json_decode($response->getContent(), true);

                expect($inspection['status'])->toBe(JsonResponse::STATUS_OK);
                expect($inspection['result']['message'])->toBe(ResponseConfig::MESSAGES['SUCCESS']);
                expect($inspection['result']['code'])->toBe(ResponseConfig::CODES['SUCCESS']);
                expect($inspection['result']['data'])->not->toBeNull();
                expect($inspection['result']['errors'])->toBe([]);
            });
        });

        // フォームバリデーション テスト
        describe('Form Validation TEST', function () {

            // [Form Validation TEST 1]  対象 「必須項目のパラメータ」 内容 「fieldの存在チェック」
            it('[Test No. ] Check If The Form Validation Item Is "Required Item Parameter" "Field Existence Check"', function () {
                $expected =  [
                    "delete_ids" => [
                        "The delete ids field is required."
                    ],
                    "delete_type" =>  [
                        "The delete type field is required."
                    ],
                ];
                $response = $this->laravel->delete($this->url, array(array()));
                $content = json_decode($response->getContent(), true);
                $inspection = $content['result']['errors']['validation'] ?? [];

                foreach ($expected as $formKey => $messages) {
                    foreach ($messages as $key => $message) {
                        expect($inspection[$formKey])->toContain($message);
                    }
                }
            });

            // [Form Validation TEST 2]  対象 削除ID「 delete_ids 」 内容 「型チェック」
            it('[Test No. ] Check If The Form Validation Item Is "delete_ids Item Parameter" "Format Check"', function () {
                $expected =  [
                    "delete_ids.0" => [
                        "The delete_ids.0 must be an integer."
                    ],
                    "delete_ids.1" =>  [
                        "The delete_ids.1 must be an integer."
                    ]
                ];

                $form = $this->data;
                $form['delete_ids'] = ['a', 'b'];
                $response = $this->laravel->delete($this->url, $form);
                $content = json_decode($response->getContent(), true);
                $inspection = $content['result']['errors']['validation'] ?? [];

                foreach ($expected as $formKey => $messages) {
                    foreach ($messages as $key => $message) {
                        expect($inspection[$formKey])->toContain($message);
                    }
                }
            });

            // [Form Validation TEST 3]  対象 削除タイプ「 delete_type 」 内容 「型チェック」
            it('[Test No. ] Check If The Form Validation Item Is "delete_type Item Parameter" "Format Check"', function () {
                $expected =  [
                    "delete_type" => [
                        "The delete type must be an integer.",
                        "The selected delete type is invalid."
                    ]
                ];

                $form = $this->data;
                $form['delete_type'] = 'a';
                $response = $this->laravel->delete($this->url, $form);
                $content = json_decode($response->getContent(), true);
                $inspection = $content['result']['errors']['validation'] ?? [];

                foreach ($expected as $formKey => $messages) {
                    foreach ($messages as $key => $message) {
                        expect($inspection[$formKey])->toContain($message);
                    }
                }
            });

            // [Form Validation TEST 4]  対象 削除タイプ 「 delete_type 」　内容 範囲チェック
            it('[Test No. ] Check If The Form Validation Item Is "delete_type Parameter" "Border Check"', function () {
                $expected =  [
                    "delete_type" => [
                        "The selected delete type is invalid."
                    ]
                ];

                $form = $this->data;
                $form['delete_type'] = 3;
                $response = $this->laravel->delete($this->url, $form);
                $content = json_decode($response->getContent(), true);
                $inspection = $content['result']['errors']['validation'] ?? [];

                foreach ($expected as $formKey => $messages) {
                    foreach ($messages as $key => $message) {
                        expect($inspection[$formKey])->toContain($message);
                    }
                }
            });
        });

        // 例外テスト
        context('[ Exception Test ]', function () {

            describe('Function Exception Test', function () {
                beforeEach(function () {
                    JsonResponse::getInstance()->setResponse(JsonResponse::getTemplate());

                    $request = new CleanStatusMasterDeleteRequest();
                    $request->merge($this->data);

                    $container = new Container();
                    $container[CleanStatusMasterContainer::KEY_REQUEST] = $request;
                    $container[CleanStatusMasterContainer::KEY_PARAMS] = new Params($request);
                    $this->diContainer = CleanStatusMasterContainer::getInstance($container)->getContainer();
                });

                afterEach(function () {
                    $this->diContainer[CleanStatusMasterContainer::KEY_MODEL1]->setTable('m_rscleaningstatus');
                });

                // [Function Exception Test 1]  「 public function delete(): void 」
                it('[Test No. ] Check If The Response Of The Function " public function delete(): void " Is Correct', function () {
                    try {
                        $this->diContainer[CleanStatusMasterContainer::KEY_MODEL1]->setTable('testing');
                        $this->diContainer[CleanStatusMasterContainer::KEY_CLEAN_STATUS_MASTER]->delete();
                    } catch (\Exception $e) {
                    }

                    $inspection = json_decode(JsonResponse::getInstance()->response()->getContent(), true);

                    expect($inspection['status'])->toBe(JsonResponse::STATUS_NG);
                    expect($inspection['result']['message'])->toBe(ResponseConfig::MESSAGES['UNEXPECTED']);
                    expect($inspection['result']['code'])->toBe(ResponseConfig::CODES['UNEXPECTED']);
                    expect(array_key_exists('exception', $inspection['result']['errors']))->toBe(true);
                });
            });
        });

        // DBテスト
        context('[ DB Test ]', function () {

            describe('DB Deleted TEST', function () {

                // [DB TEST 1]  Deleted In DB  論理削除
                it('[Test No. ] Check If The Logical Deleted In DB', function () {
                    $storedId = $this->getStoredId;
                    $storedForm = $this->storeData;
                    $storedForm['id'] = $storedId;

                    $form = $this->data;
                    $form['delete_ids'] = [$storedId];
                    $form['delete_type'] = 1;
                    $response = $this->laravel->delete($this->url, $form);

                    expect($this->laravel)->toPassSoftDeleted('m_rscleaningstatus', $storedForm, 'rsa');

                    // cleanup
                    $form['delete_type'] = 2;
                    $this->laravel->delete($this->url, $form);
                });

                // [DB TEST 2]  Deleted In DB  物理削除
                it('[Test No. ] Check If The physical Deleted In DB', function () {
                    $storedId = $this->getStoredId;
                    $storedForm = $this->storeData;
                    $storedForm['id'] = $storedId;

                    $form = $this->data;
                    $form['delete_ids'] = [$storedId];
                    $form['delete_type'] = 2;
                    $response = $this->laravel->delete($this->url, $form);

                    expect($this->laravel)->not->toPassSoftDeleted('m_rscleaningstatus', $storedForm, 'rsa');
                });
            });
        });
    });
});
