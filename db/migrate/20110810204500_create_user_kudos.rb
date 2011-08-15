class CreateUserKudos < ActiveRecord::Migration
  def self.up
    create_table :user_kudos do |t|
      t.integer :user_id
      t.integer :kudo_id
      t.timestamps
    end
  end

  def self.down
    drop_table :user_kudos
  end
end
