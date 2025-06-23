<!DOCTYPE html>
<html xmlns:th="http://www.thymeleaf.org">
<head>
    <meta charset="UTF-8">
    <title>房型管理</title>
    <link href="/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-4">
    <h3>房型管理</h3>

    <!-- 添加新房型 -->
    <form th:action="@{/admin/roomtype/save}" method="post" class="border p-3 mb-4">
        <h5>添加房型</h5>
        <input type="hidden" name="typeId">
        <div class="form-row">
            <div class="form-group col-md-2">
                <input name="name" placeholder="房型名称" class="form-control" required>
            </div>
            <div class="form-group col-md-2">
                <input name="price" type="number" step="0.01" placeholder="价格" class="form-control" required>
            </div>
            <div class="form-group col-md-2">
                <input name="capacity" type="number" placeholder="可住人数" class="form-control" required>
            </div>
            <div class="form-group col-md-3">
                <input name="description" placeholder="简介" class="form-control">
            </div>
            <div class="form-group col-md-3">
                <input name="imagePath" placeholder="图片路径" class="form-control">
            </div>
        </div>
        <button type="submit" class="btn btn-success">添加</button>
    </form>

    <!-- 房型列表 -->
    <h5>房型列表</h5>
    <table class="table table-bordered">
        <thead>
        <tr>
            <th>房型名称</th>
            <th>价格</th>
            <th>可住人数</th>
            <th>简介</th>
            <th>图片路径</th>
            <th>操作</th>
        </tr>
        </thead>
        <tbody>
        <tr th:each="type : ${roomTypeList}">
            <form th:action="@{/admin/roomtype/save}" method="post">
                <input type="hidden" name="typeId" th:value="${type.typeId}">
                <td><input name="name" class="form-control" th:value="${type.name}" required></td>
                <td><input name="price" type="number" step="0.01" class="form-control" th:value="${type.price}" required></td>
                <td><input name="capacity" type="number" class="form-control" th:value="${type.capacity}" required></td>
                <td><input name="description" class="form-control" th:value="${type.description}"></td>
                <td><input name="imagePath" class="form-control" th:value="${type.imagePath}"></td>
                <td>
                    <button type="submit" class="btn btn-primary btn-sm">保存</button>
                    <a th:href="@{'/admin/roomtype/delete/' + ${type.typeId}}"
                       class="btn btn-danger btn-sm"
                       onclick="return confirm('确认删除？')">删除</a>
                </td>
            </form>
        </tr>
        </tbody>
    </table>
</div>
</body>
</html>
