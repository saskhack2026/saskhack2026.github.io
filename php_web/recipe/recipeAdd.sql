-- =====================================================
-- INSERT COMMANDS FOR 5 ADDITIONAL RECIPES
-- =====================================================

-- 1. INSERT RECIPES
INSERT INTO recipe (recipe_name, creator_id, creation_time, modified_time) VALUES
('Lemon Garlic Shrimp', (SELECT users.user_id FROM users WHERE username='emma_eats'), NOW() + INTERVAL FLOOR(RAND() * 60) MINUTE, NOW() + INTERVAL FLOOR(RAND() * 60) MINUTE),
('Classic Hamburgers', (SELECT users.user_id FROM users WHERE username='frank_the_foodie'), NOW() + INTERVAL FLOOR(RAND() * 60) MINUTE, NOW() + INTERVAL FLOOR(RAND() * 60) MINUTE),
('Blueberry Flax Muffins', (SELECT users.user_id FROM users WHERE username='grace_gourmet'), NOW() + INTERVAL FLOOR(RAND() * 60) MINUTE, NOW() + INTERVAL FLOOR(RAND() * 60) MINUTE),
('Creamy of Carrot Soup', (SELECT users.user_id FROM users WHERE username='harry_homecook'), NOW() + INTERVAL FLOOR(RAND() * 60) MINUTE, NOW() + INTERVAL FLOOR(RAND() * 60) MINUTE),
('Grilled Corn', (SELECT users.user_id FROM users WHERE username='alice_chef'), NOW() + INTERVAL FLOOR(RAND() * 60) MINUTE, NOW() + INTERVAL FLOOR(RAND() * 60) MINUTE);

-- 2. INSERT RECIPE ACCESS (Grant access to all users for each new recipe)

-- Garlic Butter Shrimp (Recipe ID: 13)
INSERT INTO recipe_access (access_status, recipe_id, user_id)
SELECT 
    1 as access_status,
    (SELECT recipe_id FROM recipe WHERE recipe_name = 'Lemon Garlic Shrimp') as recipe_id,
    user_id
FROM users;

-- Classic Beef Tacos (Recipe ID: 14)
INSERT INTO recipe_access (access_status, recipe_id, user_id)
SELECT 
    1 as access_status,
    (SELECT recipe_id FROM recipe WHERE recipe_name = 'Classic Hamburgers') as recipe_id,
    user_id
FROM users;

-- Blueberry Flax Muffins (Recipe ID: 15)
INSERT INTO recipe_access (access_status, recipe_id, user_id)
SELECT 
    1 as access_status,
    (SELECT recipe_id FROM recipe WHERE recipe_name = 'Blueberry Flax Muffins') as recipe_id,
    user_id
FROM users;

-- Creamy Tomato Soup (Recipe ID: 16)
INSERT INTO recipe_access (access_status, recipe_id, user_id)
SELECT 
    1 as access_status,
    (SELECT recipe_id FROM recipe WHERE recipe_name = 'Creamy of Carrot Soup') as recipe_id,
    user_id
FROM users;

-- Honey Glazed Carrots (Recipe ID: 17)
INSERT INTO recipe_access (access_status, recipe_id, user_id)
SELECT 
    1 as access_status,
    (SELECT recipe_id FROM recipe WHERE recipe_name = 'Grilled Corn') as recipe_id,
    user_id
FROM users;

-- 3. INSERT NOTES FOR NEW RECIPES

INSERT INTO note (content, creation, recipe_id, note_creator_id) VALUES

-- Notes for Shrimp (recipe_id: 13)
('Quick and delicious! Perfect for weeknight dinners when you want something fancy.', NOW() + INTERVAL FLOOR(RAND() * 60) MINUTE, 
 (SELECT recipe_id FROM recipe WHERE recipe_name = 'Lemon Garlic Shrimp'), 
 (SELECT user_id FROM users WHERE username = 'alice_chef')),
('Added a splash of white wine while cooking the shrimp. Elevated the flavor tremendously!', NOW() + INTERVAL FLOOR(RAND() * 60) MINUTE, 
 (SELECT recipe_id FROM recipe WHERE recipe_name = 'Lemon Garlic Shrimp'), 
 (SELECT user_id FROM users WHERE username = 'bob_the_baker')),
('Served over angel hair pasta with fresh parsley. Restaurant quality at home!', NOW() + INTERVAL FLOOR(RAND() * 60) MINUTE, 
 (SELECT recipe_id FROM recipe WHERE recipe_name = 'Lemon Garlic Shrimp'), 
 (SELECT user_id FROM users WHERE username = 'carol_cooks')),
('Used jumbo shrimp and they were perfect. Don''t overcook or they get rubbery!', NOW() + INTERVAL FLOOR(RAND() * 60) MINUTE, 
 (SELECT recipe_id FROM recipe WHERE recipe_name = 'Lemon Garlic Shrimp'), 
 (SELECT user_id FROM users WHERE username = 'delicious_dave')),

-- Notes for Blueberry Flax Muffins (recipe_id: 15)
('These came out so fluffy and moist! The lemon zest really makes them pop.', NOW() + INTERVAL FLOOR(RAND() * 60) MINUTE, 
 (SELECT recipe_id FROM recipe WHERE recipe_name = 'Blueberry Flax Muffins'), 
 (SELECT user_id FROM users WHERE username = 'bob_the_baker')),
('Used frozen blueberries and they worked perfectly. Tossed them in flour first to prevent sinking.', NOW() + INTERVAL FLOOR(RAND() * 60) MINUTE, 
 (SELECT recipe_id FROM recipe WHERE recipe_name = 'Blueberry Flax Muffins'), 
 (SELECT user_id FROM users WHERE username = 'carol_cooks')),
('Made mini muffins for a party - perfect bite-sized treats! Adjusted baking time to 12 minutes.', NOW() + INTERVAL FLOOR(RAND() * 60) MINUTE, 
 (SELECT recipe_id FROM recipe WHERE recipe_name = 'Blueberry Flax Muffins'), 
 (SELECT user_id FROM users WHERE username = 'delicious_dave')),
('Added a lemon glaze on top while still warm. Took these from good to amazing!', NOW() + INTERVAL FLOOR(RAND() * 60) MINUTE, 
 (SELECT recipe_id FROM recipe WHERE recipe_name = 'Blueberry Flax Muffins'), 
 (SELECT user_id FROM users WHERE username = 'emma_eats')),

-- Notes for Creamy Tomato Soup (recipe_id: 16)
('Perfect comfort food for a cold day. Paired beautifully with grilled cheese sandwiches.', NOW() + INTERVAL FLOOR(RAND() * 60) MINUTE, 
 (SELECT recipe_id FROM recipe WHERE recipe_name = 'Creamy of Carrot Soup'), 
 (SELECT user_id FROM users WHERE username = 'frank_the_foodie')),
('Added fresh basil and a swirl of heavy cream. Simple ingredients, amazing results.', NOW() + INTERVAL FLOOR(RAND() * 60) MINUTE, 
 (SELECT recipe_id FROM recipe WHERE recipe_name = 'Creamy of Carrot Soup'), 
 (SELECT user_id FROM users WHERE username = 'alice_chef')),
('Made a big batch and froze portions. Great to have on hand for quick lunches!', NOW() + INTERVAL FLOOR(RAND() * 60) MINUTE, 
 (SELECT recipe_id FROM recipe WHERE recipe_name = 'Creamy of Carrot Soup'), 
 (SELECT user_id FROM users WHERE username = 'bob_the_baker')),

-- Notes for Honey Glazed Carrots (recipe_id: 17)
('Added a pinch of thyme and it elevated the whole dish. Perfect with roasted chicken.', NOW() + INTERVAL FLOOR(RAND() * 60) MINUTE, 
 (SELECT recipe_id FROM recipe WHERE recipe_name = 'Grilled Corn'), 
 (SELECT user_id FROM users WHERE username = 'delicious_dave')),
('Made these for Easter dinner and everyone raved about them. Will definitely make again!', NOW() + INTERVAL FLOOR(RAND() * 60) MINUTE, 
 (SELECT recipe_id FROM recipe WHERE recipe_name = 'Grilled Corn'), 
 (SELECT user_id FROM users WHERE username = 'frank_the_foodie'));