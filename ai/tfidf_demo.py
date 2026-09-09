import pandas as pd
from sklearn.feature_extraction.text import TfidfVectorizer

# Load training data
train_df = pd.read_csv("ai/data/train.csv")

# Get complaint text
complaints = train_df["complaint"]

# Create TF-IDF vectorizer
vectorizer = TfidfVectorizer()

# Learn vocabulary and transform the complaints
X_train = vectorizer.fit_transform(complaints)

print("Number of training complaints:", len(complaints))
print("TF-IDF matrix shape:", X_train.shape)

# Display the learned vocabulary
print("\n--- VOCABULARY ---")
print(vectorizer.get_feature_names_out())