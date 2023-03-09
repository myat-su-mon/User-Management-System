<?php
    require_once('../config/config.php');
    require_once('../config/logGen.php');
    session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-0evHe/X+R7YkIZDRvuzKMRqM+OrBnVFBL6DOitfPri4tjfHxaWutUpFmBp4vmVor" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/js/bootstrap.bundle.min.js" integrity="sha384-pprn3073KE6tl6bjs2QrFaJGz5/SUsLqktiwsUTF55Jfv3qYSDhgCecCxMW52nD2" crossorigin="anonymous"></script>
    <link href="../css/style.css" rel="stylesheet">
    <!-- jquery -->
    <script src="https://code.jquery.com/jquery-3.6.1.js" integrity="sha256-3zlB5s2uwoUzrXK3BT7AX3FyvojsraNFxCc2vC/7pNI=" crossorigin="anonymous"></script>
    <!-- datatables -->
    <link href="https://cdn.datatables.net/1.12.1/css/jquery.dataTables.min.css" rel="stylesheet">
    <script src="https://cdn.datatables.net/1.12.1/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.12.1/js/dataTables.bootstrap.js"></script>
</head>
<body>
    <div class="container">
        <form action="" method="GET" enctype="multipart/form-data">
            <div class="row">
                <div class="col-3 offset-2">
                        <div class="form-group">
                            <label for="userid" class="text-left mb-3 text-primary">ユーザID</label><br>
                            <input type="text" name="userid" id="userid" class="mb-3 w-100">
                        </div>
                        <div class="form-group">
                            <label for="age" class="text-left mb-3 text-primary">年齢</label><br>
                            <input type="text" name="age" id="age" class="mb-3 w-100">
                        </div>
                    </div>
                    <div class="col-5">
                        <div class="form-group">
                            <label for="authority" class="text-left text-primary mb-1">権限</label><br>
                            <select class="mb-3 custom-select" name="authority" id="authority">
                                <option value="">権限してください</option>
                                <option value="1">管理者</option>
                                <option value="2">一般</option>
                            </select>
                        </div>
                        <div class="form-group mb-3">
                            <label for="gender" class="text-left mb-3 text-primary">性別</label><br>
                            <input class="form-check-input" type="checkbox" name="gender" value="1"><span>男性</span>
                            <input class="form-check-input" type="checkbox" name="gender" value="2"><span>女性</span>
                            <input class="form-check-input" type="checkbox" name="gender" value="3"><span>その他</span>
                        </div>
                        <div class="form-group">
                            <label for="mobile" class="text-left mb-3 text-primary">携帯電話</label><br>
                            <input class="form-check-input" type="checkbox" name="mobile" value="1"><span>iOS</span>
                            <input class="form-check-input" type="checkbox" name="mobile" value="2"><span>Android</span>
                            <input class="form-check-input" type="checkbox" name="mobile" value="3"><span>iOS/Android</span>
                            <input class="form-check-input" type="checkbox" name="mobile" value="4"><span>なし</span>
                        </div>
                    </div>
                    <div class="col-8 offset-2">
                        <div class="form-group">
                            <label for="address" class="text-left mb-3 text-primary">住所</label><br>
                            <input type="text" name="address" id="address" class="mb-3 w-100">
                        </div>
                        <div class="form-group text-center">
                            <button type="reset" class="btn btn-primary custom-btn2 btn-w-180 mx-2" formmethod="get" onclick="location.href='../main.php'">戻る</button>
                            <button class="btn btn-primary custom-btn2 btn-w-180 mx-2" id="btnSearch">検索</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
    <div class="container" id="table-container">
        <p class='text-danger text-center fw-bold m-3' id="errMsg"></p>
        <table id="dbtable" class="table table-bordered table-hover table-striped mt-4 text-center" style="width: 900px;">
        <thead>
        </thead>
        </table>
    </div>
</body>
</html>
<script>
    $(document).ready(function() {
        getParams();
        $('#dbtable').DataTable({
            searching: false,
            ordering: false,
            paging: false,
            info: false,
            "language": {
                "emptyTable": " データはありません。"
            },
            "columnDefs": [{
                "width": "12.5%", "targets": [0, 1, 2, 3, 4, 5, 6, 7],
            }],
            "columns": [
                {"data": "userid", "title": "ユーザID"},
                {"data": "age", "title": "年齢"},
                {"data": "authority", "title": "権限"},
                {"data": "gender", "title": "性別"},
                {"data": "mobile", "title": "携帯電話"},
                {"data": "address", "title": "住所"},
                {"data": "lockstatus", "title": "状態",
                    render: function(lockstatus) {
                        return lockstatus? 'ロック' : '';
                    }
                },
                {"data": "userid", "name": "userid",
                    render: function(searchuser) {
                    data = '<a href="detail.php?searchuser=' + searchuser + '">' + '詳細' + '</a>';
                    return data;
                    }
                }
            ]
        });
        callAPI();
    });

    $('#btnSearch').on('click', function(e) {
        e.preventDefault();
        getParams();
        callAPI();
    });

    function getParams() {
        userid     = $('#userid').val() ?? '';
        age        = $('#age').val() ?? '';
        authority  = $('#authority option:selected').val() ?? '';
        address    = $('#address').val() ?? '';
        gender_arr = [];
        mobile_arr = [];

        $('input[name = "gender"]').each(function() {
            if ($(this).is(":checked")) {
                gender_arr.push(1);
            } else {
                gender_arr.push(0);
            }
        });

        $('input[name = "mobile"]').each(function() {
            if ($(this).is(":checked")) {
                mobile_arr.push(1);
            } else {
                mobile_arr.push(0);
            }
        });

        gender = gender_arr.join("|");
        mobile = mobile_arr.join("|");
    }

    function callAPI() {
        $.ajax({
            'url': '../api/userSearch.php',
            'type': 'GET',
            'dataType': 'json',
            'contentType': 'application/json',
            'data': {
                'userid': userid,
                'age': age,
                'authority': authority,
                'gender': gender,
                'mobile': mobile,
                'address': address
            },
            success: function(result, status) {
                $('#dbtable').show();
                $('#dbtable').DataTable().clear();
                $('#dbtable').DataTable().rows.add(result.data).draw();
                var errMsg = result.message;
                $('#errMsg').text(errMsg);
            },
            error: function(result, status) {
                $('#dbtable').hide();
                var errMsg = result.responseJSON.message;
                $('#errMsg').text(errMsg);
            }
        });
    }
</script>
