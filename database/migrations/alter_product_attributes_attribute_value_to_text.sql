-- Migration: Thay đổi cột attribute_value từ VARCHAR(255) sang TEXT
-- Để có thể lưu được đoạn văn dài với xuống dòng

ALTER TABLE product_attributes 
MODIFY COLUMN attribute_value TEXT NOT NULL;
