class Kudo < ActiveRecord::Base
  belongs_to :user
  belongs_to :from_user , :class_name => "User", :foreign_key => "from_id"
  
end
