class Kudo < ActiveRecord::Base
  belongs_to :user
  belongs_to :from_user , :class_name => "User", :foreign_key => "from_id"
  has_many :user_kudos 
  has_many :users, :through => :user_kudos
  
end
