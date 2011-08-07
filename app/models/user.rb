class User < ActiveRecord::Base
  # Include default devise modules. Others available are:
  # :token_authenticatable, :encryptable, :confirmable, :lockable, :timeoutable and :omniauthable
 
  
  # add :registerable to allow public sign ups       
  devise :database_authenticatable,
          :recoverable, :rememberable, :trackable, :validatable
         
  attr_accessor :login
  
  # Setup accessible (or protected) attributes for your model
  attr_accessible :email, :password, :password_confirmation, :remember_me,:login,:username,:avatar,:first_name,:last_name,:admin
  
  has_many :kudos, :order => "id desc"
  has_many :given_kudos, :class_name => "Kudo", :foreign_key => "from_id", :order => "id desc"
  
  validates_uniqueness_of :username,:email
  public
  def resize_photo(path)
    imagemagick = RAILS_ENV.eql?("production") ? "convert" : "/opt/local/bin/convert" # FOR MACPORT INSTALLS SPECIFY IMAGEMAGIC DIR
    cmd = imagemagick + " \"" + RAILS_ROOT + "/" + path + "\" -resize 40x54 -format png -quality 100 \"" + RAILS_ROOT + "/public/images/users/" + self.id.to_s + ".png\""
    system(cmd)
    if File.exists?(RAILS_ROOT + "/public/images/users/" + self.id.to_s + ".png")
      self.update_attribute("avatar",self.id.to_s + ".png")
      File.delete(RAILS_ROOT + "/" + path)
    end
  end
  
  protected
  def self.find_for_database_authentication(warden_conditions)
    conditions = warden_conditions.dup
    login = conditions.delete(:login)
    where(conditions).where(["lower(username) = :value OR lower(email) = :value", { :value => login.downcase }]).first
  end
  
  # Attempt to find a user by it's email. If a record is found, send new
  # password instructions to it. If not user is found, returns a new user
  # with an email not found error.
  def self.send_reset_password_instructions(attributes={})
    recoverable = find_recoverable_or_initialize_with_errors(reset_password_keys, attributes, :not_found)
    recoverable.send_reset_password_instructions if recoverable.persisted?
    recoverable
  end 

  def self.find_recoverable_or_initialize_with_errors(required_attributes, attributes, error=:invalid)
    (case_insensitive_keys || []).each { |k| attributes[k].try(:downcase!) }

    attributes = attributes.slice(*required_attributes)
    attributes.delete_if { |key, value| value.blank? }

    if attributes.size == required_attributes.size
      if attributes.has_key?(:login)
         login = attributes.delete(:login)
         record = find_record(login)
      else  
        record = where(attributes).first
      end  
    end  

    unless record
      record = new

      required_attributes.each do |key|
        value = attributes[key]
        record.send("#{key}=", value)
        record.errors.add(key, value.present? ? error : :blank)
      end  
    end  
    record
  end

  def self.find_record(login)
    where(["username = :value OR email = :value", { :value => login }]).first
  end
  

  
end
