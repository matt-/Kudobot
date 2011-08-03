class CreateKudos < ActiveRecord::Migration
  def self.up
    create_table :kudos do |t|
      t.integer :from_id
      t.integer :user_id
      t.text :reason
      t.string :thread_id
      t.datetime :received_at
      t.integer :rekudo
      t.boolean :sent, :default => false
      t.timestamps
    end
  end

  def self.down
    drop_table :kudos
  end
end
