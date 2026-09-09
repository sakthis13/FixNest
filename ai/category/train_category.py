import pandas as pd
import joblib

from sklearn.model_selection import train_test_split
from sklearn.feature_extraction.text import TfidfVectorizer
from sklearn.linear_model import LogisticRegression
from sklearn.metrics import accuracy_score, classification_report


# Load the 134-row seed dataset
df = pd.read_csv(r"..\data\seed_dataset.csv")

X = df["complaint"].fillna("")
y = df["category"]


# Split dataset
X_train, X_test, y_train, y_test = train_test_split(
    X,
    y,
    test_size=0.20,
    random_state=42,
    stratify=y
)


# TF-IDF
vectorizer = TfidfVectorizer(
    lowercase=True,
    ngram_range=(1, 2)
)

X_train_tfidf = vectorizer.fit_transform(X_train)
X_test_tfidf = vectorizer.transform(X_test)


# Logistic Regression
model = LogisticRegression(
    max_iter=1000,
    class_weight="balanced"
)

model.fit(X_train_tfidf, y_train)


# Test
predictions = model.predict(X_test_tfidf)

accuracy = accuracy_score(
    y_test,
    predictions
)

print("\n--- CATEGORY MODEL RESULTS ---")
print("Accuracy:", round(accuracy, 4))

print("\n--- CLASSIFICATION REPORT ---")
print(
    classification_report(
        y_test,
        predictions,
        zero_division=0
    )
)


# Save model
joblib.dump(
    model,
    "category_model.pkl"
)

joblib.dump(
    vectorizer,
    "category_vectorizer.pkl"
)

print("\nSaved:")
print("category_model.pkl")
print("category_vectorizer.pkl")