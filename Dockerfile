# 使用官方 PHP 镜像，并启用必要扩展
FROM php:8.1-cli

# 安装系统依赖和 PHP 扩展
RUN apt-get update && apt-get install -y \
    git unzip zip curl libzip-dev \
    && docker-php-ext-install zip pdo pdo_mysql

# 安装 Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 设置工作目录
WORKDIR /app

# 将项目代码拷贝进容器
COPY . .

# 安装 PHP 依赖
RUN composer install

# 开放端口
EXPOSE 8000

# 启动命令
CMD ["php", "think", "run", "-p", "8000"]
