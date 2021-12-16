Link project : https://github.com/ajinaufal/Message_API

Usage documentation : 
1.	Make sure you have MySQL, Laravel, Composer installed.
2.	Do 'composer update' without quotes, do it on the project folder.
3.	Create a database in your Mysql with the name 'chat api'.
4.	then you can import the database named 'chat_api' in the folder or use php artisan migrate to populate the required tables.
5.	Once Composer, Laravel, and MySQL are ready, you can 'php artisan serve' to use the api.


Test Documentation : 
1.	Registration and Login:
  ![Regist User 1](https://github.com/ajinaufal/Message_API/blob/laravel/public/test/Register.png)
  ![Regist User 2](https://github.com/ajinaufal/Message_API/blob/laravel/public/test/Register2.png)
  ![Barear Token](https://github.com/ajinaufal/Message_API/blob/laravel/public/test/Set_Bearer_Token.png)
 
2.	Sending Messages without Reply:
  ![Send Message Without Reply](https://github.com/ajinaufal/Message_API/blob/laravel/public/test/Send_Message_to_User_2.png)
3.	Sending Messages with Reply:
  ![Send Message With Reply](https://github.com/ajinaufal/Message_API/blob/laravel/public/test/reply_message.png)
4.	View message conversations with other people (last message and last message send):
  ![view chat](https://github.com/ajinaufal/Message_API/blob/laravel/public/test/Open_chat_from_someone.png)
5.	View the overall conversation of the account owner and last message:
  ![view chat](https://github.com/ajinaufal/Message_API/blob/laravel/public/test/view_all_conversation_and_last_message.png)
 
