# frozen_string_literal: true

source "https://rubygems.org"

git_source(:github) {|repo_name| "https://github.com/#{repo_name}" }

# gem "rails"

group :test do
    gem 'rspec'
    gem "travis"
end

local_gemfile = File.join(File.dirname(__FILE__), "Gemfile.local")

if File.exists?(local_gemfile)
    eval_gemfile local_gemfile
end