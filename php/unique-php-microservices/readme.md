# Unique PHP Microservices


### Services
- User Service (port 8101):
- POST /users
- GET /users/{id}
- Product Service (port 8102):
- GET /products
- GET /products/{id}
- Gateway Service (port 8103):
- GET /?user={id}&product={id}


### Run
```bash

-inside
cd C:\Users\wacko\Desktop\php\unique-php-microservices


C:\xampp\php\php.exe -S 127.0.0.1:8101 -t user_service


C:\xampp\php\php.exe -S 127.0.0.1:8102 -t product_service


C:\xampp\php\php.exe -S 127.0.0.1:8103 -t gateway_service

-Create a user:
curl -X POST http://127.0.0.1:8101/users -H "Content-Type: application/json" -d "{\"name\":\"Nikolai\",\"email\":\"nikolai@mail.com\"}"

-Get product:
curl http://127.0.0.1:8102/products/1

-gateways
curl "http://127.0.0.1:8103/?user=1&product=1"

http://127.0.0.1:8101/users/1 → should return Alice/Eve (depending on your seed data).

http://127.0.0.1:8102/products/1 → should return Widget/Book.


http://127.0.0.1:8103/?user=1&product=1 → should aggregate both.


-running locust

from locust import HttpUser, task, between

class GatewayUser(HttpUser):
    wait_time = between(1, 3)

    @task
    def get_user_and_product(self):
        # Calls the gateway service (adjust ports if different)
        self.client.get("/?user=1&product=1")