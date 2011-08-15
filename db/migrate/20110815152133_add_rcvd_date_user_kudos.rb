class AddRcvdDateUserKudos < ActiveRecord::Migration
  def self.up
    add_column :user_kudos, :received_date, :datetime
  end

  def self.down
    remove_column :user_kudos, :received_date
  end
end
