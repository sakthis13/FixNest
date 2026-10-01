import pandas as pd
import joblib

from sklearn.model_selection import train_test_split
from sklearn.feature_extraction.text import TfidfVectorizer
from sklearn.linear_model import LogisticRegression
from sklearn.metrics import accuracy_score, classification_report

df = pd.read_csv("severity_dataset.csv")

X = df["complaint"]
y = df["severity"]


X_train, X_test, y_train, y_test = train_test_split(
    X,
    y,
    test_size=0.20,
    random_state=42,
    stratify=y
)

vectorizer = TfidfVectorizer(
    lowercase=True,
    ngram_range=(1, 2)
)

X_train_tfidf = vectorizer.fit_transform(X_train)
X_test_tfidf = vectorizer.transform(X_test)

model = LogisticRegression(
    max_iter=1000,
    class_weight="balanced"
)

model.fit(X_train_tfidf, y_train)


predictions = model.predict(X_test_tfidf)

accuracy = accuracy_score(y_test, predictions)

print("\n--- MODEL RESULTS ---")
print("Accuracy:", round(accuracy, 4))

print("\n--- CLASSIFICATION REPORT ---")
print(classification_report(y_test, predictions))


joblib.dump(model, "severity_model.pkl")
joblib.dump(vectorizer, "severity_vectorizer.pkl")

print("\nModel saved:")
print("severity_model.pkl")
print("severity_vectorizer.pkl")