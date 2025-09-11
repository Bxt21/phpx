#UserSide
# Get all users
Invoke-WebRequest -Uri "http://127.0.0.1:8101/users" -Method GET

# Get single user by ID
Invoke-WebRequest -Uri "http://127.0.0.1:8101/users/1" -Method GET

# Add new user
Invoke-WebRequest -Uri "http://127.0.0.1:8101/users" -Method POST -Body '{"name":"wacko","email":"wacko@mail.com"}' -ContentType "application/json"

# Update user (PUT)
Invoke-WebRequest -Uri "http://127.0.0.1:8101/users/1" -Method PUT -Body '{"email":"newalice@mail.com"}' -ContentType "application/json"

# Delete user
Invoke-WebRequest -Uri "http://127.0.0.1:8101/users/1" -Method DELETE


#ProductSide
# Get all products
Invoke-WebRequest -Uri "http://127.0.0.1:8102/products" -Method GET

# Get single product by ID
Invoke-WebRequest -Uri "http://127.0.0.1:8102/products/1" -Method GET

# Add new product
Invoke-WebRequest -Uri "http://127.0.0.1:8102/products" -Method POST -Body '{"name":"Notebook","price":3.75}' -ContentType "application/json"

# Update product (PUT)
Invoke-WebRequest -Uri "http://127.0.0.1:8102/products/1" -Method PUT -Body '{"price":4.0}' -ContentType "application/json"

# Delete product
Invoke-WebRequest -Uri "http://127.0.0.1:8102/products/1" -Method DELETE



#Gateway

#useronly
curl "http://127.0.0.1:8103?user=1"

#productonly
curl "http://127.0.0.1:8103?product=2"

#both
curl "http://127.0.0.1:8103?user=1&product=2"



